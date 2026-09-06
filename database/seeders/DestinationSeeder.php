<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Bali, Indonesia',
                'slug' => 'bali-indonesia',
                'description' => 'A tropical paradise known for its iconic rice paddies, stunning beaches, and coral reefs.',
                'image' => 'https://example.com/images/destinations/bali.jpg',
                'status' => 1,
            ],
            [
                'name' => 'Kyoto, Japan',
                'slug' => 'kyoto-japan',
                'description' => 'Famous for its classical Buddhist temples, as well as gardens, imperial palaces, and Shinto shrines.',
                'image' => 'https://example.com/images/destinations/kyoto.jpg',
                'status' => 1,
            ],
            [
                'name' => 'Swiss Alps, Switzerland',
                'slug' => 'swiss-alps',
                'description' => 'Dramatic mountain landscapes perfect for winter sports and summer alpine hiking.',
                'image' => 'https://example.com/images/destinations/swiss-alps.jpg',
                'status' => 1,
            ],
            [
                'name' => 'Santorini, Greece',
                'slug' => 'santorini-greece',
                'description' => 'Iconic whitewashed, cubiform houses built on rugged cliffs overlooking the Aegean Sea.',
                'image' => 'https://example.com/images/destinations/santorini.jpg',
                'status' => 1,
            ]
        ];
        // Loop through and create each record individually
        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
