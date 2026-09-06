<?php

namespace Database\Seeders;

use App\Models\TourItinerary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itineraries = [
            [
                'tour_id' => 1,
                'day_number' => 1,
                'title' => 'Arrival and Welcome Dinner',
                'description' => 'Arrive at the airport where our guide will meet you and transfer you to the hotel. Enjoy a traditional welcome dinner in the evening.',
                'meals_included' => 'Dinner',
                'status' => 'ACTIVE', // Adjust to match your specific ENUM values
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'day_number' => 2,
                'title' => 'Temple Exploration and Rice Terraces',
                'description' => 'Spend the day exploring ancient water temples and walking through the iconic terraced rice paddies. A local lunch will be served.',
                'meals_included' => 'Breakfast, Lunch',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'day_number' => 3,
                'title' => 'Free Morning and Departure',
                'description' => 'Enjoy a relaxed morning at the resort or do some souvenir shopping before your transfer back to the airport.',
                'meals_included' => 'Breakfast',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        TourItinerary::insert($itineraries);
    }
}
