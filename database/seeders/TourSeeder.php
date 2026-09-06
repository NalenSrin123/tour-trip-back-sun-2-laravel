<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = [
            [
                'category_id' => 3, // Beach Getaways
                'destination_id' => 1, // Bali, Indonesia
                'title' => '3-Day Exotic Bali Getaway',
                'slug' => '3-day-exotic-bali-getaway',
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 499.00,
                'price_override' => null, // Regular price
                'status' => 'PUBLISHED',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2, // Cultural & Historical
                'destination_id' => 2, // Kyoto, Japan
                'title' => '5-Day Kyoto Heritage Tour',
                'slug' => '5-day-kyoto-heritage-tour',
                'duration_days' => 5,
                'duration_nights' => 4,
                'base_price' => 850.00,
                'price_override' => 799.00, // On sale!
                'status' => 'PUBLISHED',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1, // Adventure & Outdoors
                'destination_id' => 3, // Swiss Alps
                'title' => 'Swiss Alps Winter Expedition',
                'slug' => 'swiss-alps-winter-expedition',
                'duration_days' => 7,
                'duration_nights' => 6,
                'base_price' => 1200.00,
                'price_override' => null,
                'status' => 'DRAFT', // Hidden from public guests
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Example insertion:
        Tour::insert($tours);
    }
}
