<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories =
            [
                [
                    'name' => 'Adventure & Outdoors',
                    'slug' => 'adventure-and-outdoors',
                    'description' => 'Thrilling trips focused on hiking, rafting, and exploring nature.',
                    'image' => 'https://example.com/images/categories/adventure.jpg',
                    'status' => 1,
                ],
                [
                    'name' => 'Cultural & Historical',
                    'slug' => 'cultural-and-historical',
                    'description' => 'Immersive experiences exploring ancient ruins, museums, and local traditions.',
                    'image' => 'https://example.com/images/categories/cultural.jpg',
                    'status' => 1,
                ],
                [
                    'name' => 'Beach Getaways',
                    'slug' => 'beach-getaways',
                    'description' => 'Relaxing vacations at the most beautiful beaches and tropical islands.',
                    'image' => 'https://example.com/images/categories/beaches.jpg',
                    'status' => 1,
                ],
                [
                    'name' => 'Wildlife Safari',
                    'slug' => 'wildlife-safari',
                    'description' => 'Guided tours to observe exotic animals in their natural habitats.',
                    'image' => 'https://example.com/images/categories/safari.jpg',
                    'status' => 1,
                ]
            ];
        // Loop through and create each record individually
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
