<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LtiResourceLinkEntryResource extends JsonResource
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
            'resource_link' => new LtiResourceLinkResource($this->whenLoaded('resourceLink')),
            'is_staff' => $this->is_staff,
            'score' => $this->isCompleted() ? [
                'score' => $this->score,
                'score_maximum' => $this->score_maximum,
                'score_percentage' => $this->score_maximum != 0
                    ? ($this->score / $this->score_maximum) * 100
                    : 100, // if maximum is 0, consider it 100%
                'completed_at' => $this->completed_at,
                'submitted_at' => $this->submitted_at,
                'submission_success' => $this->submission_success,
                'submission_error' => $this->submission_error,
            ] : null,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
