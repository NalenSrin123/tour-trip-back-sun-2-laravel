<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourItineraryResource extends JsonResource
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
            'day_number' => $this->day_number,
            'title' => $this->title,
            'description' => $this->description,
            'meals_included' => $this->meals_included,
            'status' => $this->status
        ];
    }
}
