<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'base_price' => $this->base_price,
            'price_override' => $this->price_override,
            'duration_day' => $this->duration_days,
            'duration_night' => $this->duration_nights,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'destination' => new DestinationResource($this->whenLoaded('destination')),
            'tourImages' => TourImageResource::collection($this->whenLoaded('tourImages')),
            'includeExclude' => IncludeExcludeResource::collection($this->whenLoaded('includeExclude')),
            'tourItinerary' => TourItineraryResource::collection($this->whenLoaded('tourItinerary')),
            'tourSchedule' => TourScheduleResource::collection($this->whenLoaded('tourSchedule')),
        ];
    }
}
