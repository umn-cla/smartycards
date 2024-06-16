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
            'cards_count' => $this->when(isset($this->cards_count), $this->cards_count),
            'memberships_count' => $this->when(isset($this->memberships_count), $this->memberships_count),
            'cards' => CardResource::collection($this->whenLoaded('cards')),
            'memberships' => DeckMembershipResource::collection($this->whenLoaded('memberships')),
            'capabilities' => [
                'canUpdate' => $request->user()->can('update', $this->resource),
                'canDelete' => $request->user()->can('delete', $this->resource),
                'canViewMemberships' => $request->user()->can('viewMemberships', $this->resource),
                'canCreateMembership' => $request->user()->can('createMembership', $this->resource),
            ],
        ];
    }
}
