<?php

use App\Models\Activity;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $activities = [
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

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Activity::truncate();
    }
};
