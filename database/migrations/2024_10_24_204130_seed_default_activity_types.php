<?php

use App\Models\ActivityType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $activity_types = [
            [
                'slug' => 'create-card',
                'name' => 'Create Card',
                'description' => 'Make a new card in a deck',
                'default_xp' => 25,
            ],
            [
                'slug' => 'practice-card',
                'name' => 'Practice Card',
                'description' => 'Practice a single card in a deck',
                'default_xp' => 1,
            ],
            [
                'slug' => 'practice-all-cards',
                'name' => 'Practice Deck',
                'description' => 'Practice all cards in a deck',
                'default_xp' => 100,
            ],
            [
                'slug' => 'quiz',
                'name' => 'Quiz',
                'description' => 'Take a quiz on a deck',
                'default_xp' => 100,
            ],
            [
                'slug' => 'matching',
                'name' => 'Matching',
                'description' => 'Play a matching game',
                'default_xp' => 100,
            ],
        ];

        foreach ($activity_types as $activity) {
            ActivityType::create($activity);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ActivityType::truncate();
    }
};
