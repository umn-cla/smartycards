<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LtiResourceLinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $entry = $this->relationLoaded('entries')
            ? $this->entries->first()
            : null;

        return [
            'id' => $this->id,
            'resource_link_id' => $this->resource_link_id,
            'title' => $this->title,
            'context_id' => $this->context_id,
            'context_title' => $this->context_title,
            'context_label' => $this->context_label,
            'current_user_role' => $entry?->is_staff
                ? 'staff'
                : 'student',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
