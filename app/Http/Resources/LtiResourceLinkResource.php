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

        return [
            'id' => $this->id,
            'lti_deployment_id' => $this->lti_deployment_id,
            'resource_link_id' => $this->resource_link_id,
            'title' => $this->title,
            'description' => $this->description,
            'context_id' => $this->context_id,
            'context_title' => $this->context_title,
            'context_label' => $this->context_label,
            'custom_params' => $this->custom_params,
            'lineitem_url' => $this->lineitem_url,
            'lineitems_url' => $this->lineitems_url,
            'ags_scopes' => $this->ags_scopes,
            'canvas_url' => $this->getCanvasUrl(),
            'entries' => LtiResourceLinkEntryResource::collection($this->whenLoaded('entries')),
            'settings' => $this->settings,
            'deck_id' => $this->deck_id,
            'deck' => DeckResource::make($this->whenLoaded('deck')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
