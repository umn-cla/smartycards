<?php

namespace App\Http\Resources;

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
            'is_public' => $this->is_public,

            'cards_count' => $this->when(isset($this->cards_count), $this->cards_count),

            'memberships_count' => $this->when(isset($this->memberships_count), $this->memberships_count),

            'last_attempted_at' => $this->when(isset($this->last_attempted_at), $this->last_attempted_at),

            'avg_score' => $this->when(isset($this->avg_score), $this->avg_score),

            'cards' => CardResource::collection($this->whenLoaded('cards')),

            'memberships' => DeckMembershipResource::collection($this->whenLoaded('memberships')),

            'current_user_role' => $this->current_user_role,

            'capabilities' => [
                'canUpdate' => $request->user()->can('update', $this->resource),
                'canDelete' => $request->user()->can('delete', $this->resource),
                'canViewMemberships' => $request->user()->can('viewMemberships', $this->resource),
                'canCreateMembership' => $request->user()->can('createMembership', $this->resource),
                'canLeave' => $request->user()->can('leave', $this->resource),
                // only allow joining as viewer if the user is not a member of the deck
                'canJoinAsViewer' => $this->current_user_role === null && $request->user()->can('joinAsViewer', $this->resource),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
