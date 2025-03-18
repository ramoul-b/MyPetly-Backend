<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Training', 'icon' => 'self-improvement', 'type' => 'material', 'color' => '#FFBE5E'],
            ['name' => 'Grooming', 'icon' => 'spa', 'type' => 'material', 'color' => '#E57C94'],
            ['name' => 'Pet-sitter', 'icon' => 'shopping-bag', 'type' => 'material', 'color' => '#795548'],
            ['name' => 'Accessories', 'icon' => 'shopping-bag', 'type' => 'material', 'color' => '#795548'],
            ['name' => 'Grooming', 'icon' => 'spa', 'type' => 'material', 'color' => '#E57C94'],
            ['name' => 'Pet-sitter', 'icon' => 'self-improvement', 'type' => 'material', 'color' => '#FFBE5E'],
            ['name' => 'Adoption', 'icon' => 'favorite', 'type' => 'material', 'color' => '#F44336'],
            ['name' => 'Lost & Found', 'icon' => 'search', 'type' => 'material', 'color' => '#607D8B'],
            ['name' => 'Pet-sitter', 'icon' => 'person', 'type' => 'material', 'color' => '#00BCD4'],
            ['name' => 'Adoption', 'icon' => 'favorite', 'type' => 'material', 'color' => '#F44336'],
            ['name' => 'Training', 'icon' => 'self-improvement', 'type' => 'material', 'color' => '#FFBE5E'],
            ['name' => 'Vaccination', 'icon' => 'vaccines', 'type' => 'material', 'color' => '#4CAF50'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
