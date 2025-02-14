<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'deck_id' => $this->deck_id,
            'front' => $this->front,
            'back' => $this->back,
            'attempts_count' => $this->attempts_count,
            'avg_score' => $this->avg_score,
            'last_attempt_at' => $this->last_attempt_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'capabilities' => [
                'canUpdate' => $request->user()->can('update', $this->resource),
                'canDelete' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
