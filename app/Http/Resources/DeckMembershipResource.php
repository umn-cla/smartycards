<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeckMembershipResource extends JsonResource
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
            'deck_id' => $this->deck_id,
            'user_id' => $this->user_id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'role' => $this->role,
            'share_link' => $this->when($this->share_link, $this->share_link),
            'capabilities' => [
                'canUpdate' => $request->user()->can('update', $this->resource),
                'canDelete' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
