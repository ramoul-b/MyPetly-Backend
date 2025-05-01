<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Veterinary',
                    'fr' => 'Vétérinaire',
                    'it' => 'Veterinario'
                ],
                'icon' => 'medical-services',
                'type' => 'material',
                'color' => '#6D9EEB',
            ],
            [
                'name' => [
                    'en' => 'Vaccination',
                    'fr' => 'Vaccination',
                    'it' => 'Vaccinazione'
                ],
                'icon' => 'vaccines',
                'type' => 'material',
                'color' => '#4CAF50',
            ],
            [
                'name' => [
                    'en' => 'Grooming',
                    'fr' => 'Toilettage',
                    'it' => 'Toelettatura'
                ],
                'icon' => 'spa',
                'type' => 'material',
                'color' => '#E57C94',
            ],
            [
                'name' => [
                    'en' => 'Pet-sitting',
                    'fr' => 'Garde d’animaux',
                    'it' => 'Pet-sitting'
                ],
                'icon' => 'home',
                'type' => 'material',
                'color' => '#2196F3',
            ],
            [
                'name' => [
                    'en' => 'Training',
                    'fr' => 'Dressage',
                    'it' => 'Addestramento'
                ],
                'icon' => 'self-improvement',
                'type' => 'material',
                'color' => '#FFBE5E',
            ],
            [
                'name' => [
                    'en' => 'Nutrition',
                    'fr' => 'Nutrition',
                    'it' => 'Nutrizione'
                ],
                'icon' => 'restaurant',
                'type' => 'material',
                'color' => '#FFA500',
            ],
            [
                'name' => [
                    'en' => 'Accessories',
                    'fr' => 'Accessoires',
                    'it' => 'Accessori'
                ],
                'icon' => 'shopping-bag',
                'type' => 'material',
                'color' => '#795548',
            ],
            [
                'name' => [
                    'en' => 'Adoption',
                    'fr' => 'Adoption',
                    'it' => 'Adozione'
                ],
                'icon' => 'favorite',
                'type' => 'material',
                'color' => '#F44336',
            ],
            [
                'name' => [
                    'en' => 'Lost & Found',
                    'fr' => 'Perdus & Retrouvés',
                    'it' => 'Persi e trovati'
                ],
                'icon' => 'search',
                'type' => 'material',
                'color' => '#607D8B',
            ],
            [
                'name' => [
                    'en' => 'Insurance',
                    'fr' => 'Assurance',
                    'it' => 'Assicurazione'
                ],
                'icon' => 'verified',
                'type' => 'material',
                'color' => '#673AB7',
            ],
            [
                'name' => [
                    'en' => 'Events',
                    'fr' => 'Événements',
                    'it' => 'Eventi'
                ],
                'icon' => 'event',
                'type' => 'material',
                'color' => '#3F51B5',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
