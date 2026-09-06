<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourImageResource extends JsonResource
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
            'image_url' => $this->image_url,
            'is_primary' => $this->is_primary,
            'status' => $this->status
        ];
    }
}
