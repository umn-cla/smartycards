<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'cards_count' => $this->when(isset($this->cards_count), $this->cards_count),

            'memberships_count' => $this->when(isset($this->memberships_count), $this->memberships_count),

            'last_attempted_at' => Carbon::parse($this->last_attempted_at)->toIso8601String(),

            'avg_card_score' => $this->when(isset($this->avg_card_score), $this->avg_card_score),

            'cards' => CardResource::collection($this->whenLoaded('cards')),
            'memberships' => DeckMembershipResource::collection($this->whenLoaded('memberships')),
            'current_user_role' => $this->when(
                isset($this->currentUserMemberships) && $this->currentUserMemberships->count() >= 1,
                $this->currentUserMemberships->first()->role
            ),
            'capabilities' => [
                'canUpdate' => $request->user()->can('update', $this->resource),
                'canDelete' => $request->user()->can('delete', $this->resource),
                'canViewMemberships' => $request->user()->can('viewMemberships', $this->resource),
                'canCreateMembership' => $request->user()->can('createMembership', $this->resource),
                'canLeave' => $request->user()->can('leave', $this->resource),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
