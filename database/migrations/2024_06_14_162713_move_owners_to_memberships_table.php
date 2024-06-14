<?php

use App\Models\Deck;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Deck::all()->each(function (Deck $deck) {
            $deck->memberships()->create([
                'user_id' => $deck->owner_id,
                'role' => 'owner',
            ]);
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
        Schema::table('memberships', function (Blueprint $table) {
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        // move owners back to owner_id
        Deck::all()->each(function (Deck $deck) {
            $deck->update(['owner_id' => $deck->owner->id]);
        });

        // remove owners from memberships
        Deck::all()->each(function (Deck $deck) {
            $deck->memberships()->where('role', 'owner')->delete();
        });
    }
};
