<?php

use App\Enums\ActivityTypeEnum;
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
                'name' => ActivityTypeEnum::CREATE_CARD->value,
                'label' => 'Create Card',
                'description' => 'Make a new card in a deck',
                'default_xp' => 25,
            ],
            [
                'name' => ActivityTypeEnum::PRACTICE_CARD->value,
                'label' => 'Practice Deck',
                'description' => 'Practice all cards in a deck',
                'default_xp' => 100,
            ],
            [
                'name' => ActivityTypeEnum::PRACTICE_ALL_CARDS->value,
                'label' => 'Quiz',
                'description' => 'Take a quiz on a deck',
                'default_xp' => 100,
            ],
            [
                'name' => ActivityTypeEnum::QUIZ->value,
                'label' => 'Matching',
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
