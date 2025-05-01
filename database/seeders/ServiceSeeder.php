<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => ['en' => 'Vet', 'fr' => 'Vétérinaire'],     'icon' => 'medical-services',     'color' => '#6D9EEB'],
            ['name' => ['en' => 'Training', 'fr' => 'Dressage'],   'icon' => 'self-improvement',     'color' => '#FFBE5E'],
            ['name' => ['en' => 'Grooming', 'fr' => 'Toilettage'], 'icon' => 'spa',                  'color' => '#E57C94'],
            ['name' => ['en' => 'Food', 'fr' => 'Nourriture'],     'icon' => 'restaurant',           'color' => '#FFA500'],
            ['name' => ['en' => 'Medicine', 'fr' => 'Médicaments'],'icon' => 'medication',           'color' => '#4CAF50'],
            ['name' => ['en' => 'Accessories', 'fr' => 'Accessoires'],'icon' => 'shopping-bag',     'color' => '#795548'],
            ['name' => ['en' => 'Pet-sitter', 'fr' => 'Garde d\'animaux'], 'icon' => 'home',         'color' => '#2196F3'],
            ['name' => ['en' => 'Walking', 'fr' => 'Promenade'],   'icon' => 'directions-walk',      'color' => '#9C27B0'],
            ['name' => ['en' => 'Adoption', 'fr' => 'Adoption'],   'icon' => 'favorite',             'color' => '#F44336'],
            ['name' => ['en' => 'Lost & Found', 'fr' => 'Animaux perdus'],'icon' => 'search',       'color' => '#607D8B'],
            ['name' => ['en' => 'Insurance', 'fr' => 'Assurance'], 'icon' => 'verified',             'color' => '#673AB7'],
            ['name' => ['en' => 'Events', 'fr' => 'Événements'],   'icon' => 'event',                'color' => '#3F51B5'],
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'description' => null,
                'icon' => $service['icon'],
                'color' => $service['color'],
                'active' => true,
                'category_id' => 1 
            ]);
        }
    }
}
