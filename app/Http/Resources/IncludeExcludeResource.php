<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncludeExcludeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tour_id' => $this->tour_id,
            'type' => $this->type,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
