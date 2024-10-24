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
                'name' => 'Create Card',
                'description' => 'Make a new card in a deck',
                'default_xp' => 25,
            ],
            [
                'name' => 'Practice',
                'description' => 'Practice a deck',
                'default_xp' => 100,
            ],
            [
                'name' => 'Quiz',
                'description' => 'Take a quiz on a deck',
                'default_xp' => 100,
            ],
            [
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
