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

            'current_user_details' => [
                'role' => $this->current_user_role ?? null,
                'xp' => $this->current_user_xp ?? 0,
                'last_activity_at' => $this->last_activity_at ?? null,
            ],

            'cards' => CardResource::collection($this->whenLoaded('cards')),

            'memberships' => DeckMembershipResource::collection($this->whenLoaded('memberships')),

            // TODO: use current_user_details.role instead
            'current_user_role' => $this->current_user_role,

            'capabilities' => [
                'canUpdate' => $request->user()->can('update', $this->resource),
                'canDelete' => $request->user()->can('delete', $this->resource),
                'canViewMemberships' => $request->user()->can('viewMemberships', $this->resource),
                'canCreateMembership' => $request->user()->can('createMembership', $this->resource),
                'canViewReports' => $request->user()->can('viewReports', $this->resource),
                'canLeave' => $request->user()->can('leave', $this->resource),
                // only allow joining as viewer if the user is not a member of the deck
                'canJoinAsViewer' => $this->current_user_role === null && $request->user()->can('joinAsViewer', $this->resource),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
