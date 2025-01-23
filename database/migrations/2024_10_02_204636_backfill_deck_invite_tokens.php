<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Retrieve all decks
        $decks = DB::table('decks')->select('id')->get();

        foreach ($decks as $deck) {
            // Check and create tokens for each permission type
            foreach (['view', 'edit'] as $permission) {
                $existingToken = DB::table('deck_invite_tokens')
                    ->where('deck_id', $deck->id)
                    ->where('permission', $permission)
                    ->first();

                if (! $existingToken) {
                    DB::table('deck_invite_tokens')->insert([
                        'deck_id' => $deck->id,
                        'permission' => $permission,
                        'token' => Str::random(32),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No actions needed for down migration
    }
};
