<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DeckMembership extends Model implements AuditableContract
{
    use AuditableTrait, HasFactory;

    const ROLE_OWNER = 'owner';

    const ROLE_EDITOR = 'editor';

    const ROLE_VIEWER = 'viewer';

    protected $fillable = [
        'deck_id',
        'user_id',
        'role',
    ];

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->hasManyThrough(Card::class, Deck::class, 'id', 'deck_id', 'deck_id', 'id');
    }

    public function scopeWithUniqueCardAttemptCount($query)
    {
        return $query->addSelect([
            'unique_card_attempts_count' => DB::table('card_attempts')
                ->selectRaw('count(distinct card_id)')
                ->whereColumn('user_id', 'deck_memberships.user_id')
                ->whereColumn('deck_id', 'deck_memberships.deck_id'),
        ]);
    }

    /**
     * Scope to check if all cards in a deck have been attempted
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithHasAttemptedAllCards($query)
    {
        return $query->select([
            // 'deck_memberships.*',
            'deck_memberships.deck_id',
            'deck_memberships.user_id',
            DB::raw('(COUNT(cards.id) = COUNT(DISTINCT card_attempts.card_id)) as has_attempted_all_cards'),
        ])
            ->leftJoin('cards', 'cards.deck_id', '=', 'deck_memberships.deck_id')
            ->leftJoin('card_attempts', function ($join) {
                $join->on('card_attempts.card_id', '=', 'cards.id')
                    ->where('card_attempts.user_id', '=', DB::raw('deck_memberships.user_id'));
            })
            ->groupBy('deck_memberships.deck_id', 'deck_memberships.user_id')
            ->withCasts([
                'has_attempted_all_cards' => 'boolean',
            ]);
    }

    public function scopeWithHasActivity($query, ActivityTypeEnum $activityType)
    {

        $lowercaseActivityType = strtolower($activityType->value);

        $columnName = "has_{$lowercaseActivityType}_activity";

        return $query
            ->addSelect([
                $columnName => ActivityEvent::selectRaw('COUNT(*) > 0')
                    ->leftJoin(
                        'activity_types',
                        'activity_events.activity_type_id',
                        '=',
                        'activity_types.id'
                    )
                    ->whereColumn('deck_memberships.user_id', 'activity_events.user_id')
                    ->where('activity_types.name', $activityType->value),
            ]);
    }

    public function scopeWithStats($query)
    {
        return $query
            ->withHasAttemptedAllCards()
            ->withHasActivity(ActivityTypeEnum::QUIZ)
            ->withHasActivity(ActivityTypeEnum::MATCHING);
    }

    /**
     * adds user participation details to the query,
     * including whether the user has:
     * - attempted all cards in the deck,
     */
    // public function scopeWithParticipation($query)
    // {
    //     $hasAttemptedAllCardsSubquery = CardAttempt::selectRaw(
    //         'count(distinct card_id) = deck.cards_count'
    //     )->whereColumn('deck_id', 'deck_memberships.deck_id');

    //     $hasQuizActivity = ActivityEvent::selectRaw('count(*) > 0')
    //         ->where('user_id', $this->user_id)
    //         ->where('event_type', 'quiz')
    //         ->whereColumn('eventable_id', 'deck_memberships.deck_id');

    //     $hasMatchingGameActivity = ActivityEvent::selectRaw('count(*) > 0')
    //         ->where('user_id', $this->user_id)
    //         ->where('event_type', 'matching_game')
    //         ->whereColumn('eventable_id', 'deck_memberships.deck_id');

    //     return $query
    //         ->addSelect([
    //             'has_attempted_all_cards' => $hasAttemptedAllCardsSubquery,
    //             'has_quiz_activity' => $hasQuizActivity,
    //             'has_matching_game_activity' => $hasMatchingGameActivity,
    //         ]);
    // }
}
