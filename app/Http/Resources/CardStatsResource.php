<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardStatsResource extends JsonResource
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
            'attempts_count' => $this->attempts_count,
            'avg_score' => $this->avg_score,
            'last_attempted_at' => $this->last_attempted_at,
        ];
    }
}
