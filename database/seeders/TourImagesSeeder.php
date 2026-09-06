<?php

namespace Database\Seeders;

use App\Models\TourImages;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tourImages = [
            [
                'tour_id' => 1,
                'image_url' => 'https://example.com/images/tours/bali-main.jpg',
                'is_primary' => true,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'image_url' => 'https://example.com/images/tours/bali-temple.jpg',
                'is_primary' => false,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'image_url' => 'https://example.com/images/tours/bali-hotel.jpg',
                'is_primary' => false,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        TourImages::insert($tourImages);
    }
}
