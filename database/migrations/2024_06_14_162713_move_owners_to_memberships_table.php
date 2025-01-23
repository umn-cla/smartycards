<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert owners into memberships table
        DB::table('decks')->select('id', 'owner_id')->chunkById(100, function ($decks) {
            foreach ($decks as $deck) {
                DB::table('memberships')->insert([
                    'deck_id' => $deck->id,
                    'user_id' => $deck->owner_id,
                    'role' => 'owner',
                ]);
            }
        });

        Schema::table('decks', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decks', function (Blueprint $table) {
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        // Move owners back to owner_id
        DB::table('memberships')->where('role', 'owner')->chunkById(100, function ($memberships) {
            foreach ($memberships as $membership) {
                DB::table('decks')
                    ->where('id', $membership->deck_id)
                    ->update(['owner_id' => $membership->user_id]);
            }
        });

        // Remove owners from memberships
        DB::table('memberships')->where('role', 'owner')->delete();
    }
};
