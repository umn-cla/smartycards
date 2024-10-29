<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
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

    protected $casts = [
        'has_attempted_all_cards' => 'boolean',
        'has_quiz_activity' => 'boolean',
        'has_matching_activity' => 'boolean',
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

    /**
     * Scope to check if all cards in a deck have been attempted
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithHasAttemptedAllCards($query)
    {
        return $query
            // keep exising columns
            ->addSelect(['*'])
            // and add new column
            ->selectRaw('
                (SELECT
                    COUNT(DISTINCT cards.id) = COUNT(DISTINCT card_attempts.card_id)
                FROM cards
                LEFT JOIN card_attempts ON
                    card_attempts.card_id = cards.id
                    AND card_attempts.user_id = deck_memberships.user_id
                WHERE cards.deck_id = deck_memberships.deck_id
                ) as has_attempted_all_cards
            ');
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
}
