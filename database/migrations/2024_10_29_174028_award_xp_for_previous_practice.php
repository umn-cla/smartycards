<?php

use App\Enums\ActivityTypeEnum;
use App\Models\ActivityEvent;
use App\Models\CardAttempt;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $attempts = CardAttempt::query()
            ->with(['user', 'deck'])
            ->select(['deck_id', 'user_id', DB::raw('count(*) as attempts_count')])
            ->join('cards', 'cards.id', '=', 'card_attempts.card_id')
            ->groupBy(['deck_id', 'user_id'])
            ->get();

        foreach ($attempts as $attempt) {
            // award 1 xp per attempt
            ActivityEvent::awardXP(
                userId: $attempt->user_id,
                deckId: $attempt->deck_id,
                activityType: ActivityTypeEnum::PRACTICE_CARD,
                xp: $attempt->attempts_count
            );
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
