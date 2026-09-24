<?php

namespace Database\Seeders;

use App\Models\IncludedExcluded;
use App\Models\TourItinerary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InclduedExcludedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $includedExcluded = [
            [
                'tour_id' => 1,
                'type' => 'INCLUDED',
                'description' => '3 nights accommodation in a 4-star hotel',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'type' => 'INCLUDED',
                'description' => 'All ground transportation during the tour',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'type' => 'EXCLUDED',
                'description' => 'International flights and airport taxes',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tour_id' => 1,
                'type' => 'EXCLUDED',
                'description' => 'Travel insurance and personal expenses',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        IncludedExcluded::insert($includedExcluded);
    }
}
