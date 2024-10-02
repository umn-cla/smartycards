<?php

use App\Models\Deck;
use App\Models\DeckInviteToken;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $decks = Deck::all();

        foreach ($decks as $deck) {
            // Check and create tokens for each permission type
            foreach (['view', 'edit'] as $permission) {
                $existingToken = $deck->tokens()->where('permission', $permission)->first();

                if (! $existingToken) {
                    DeckInviteToken::create([
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
        //
    }
};
