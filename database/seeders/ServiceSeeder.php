<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => ['en' => 'Veterinary', 'fr' => 'Vétérinaire', 'it' => 'Veterinario'],
                'description' => ['en' => 'General animal health service', 'fr' => 'Service général de santé animale', 'it' => 'Servizio generale di salute animale'],
                'icon' => 'medical-services',
                'color' => '#6D9EEB',
                'category_id' => 1
            ],
            [
                'name' => ['en' => 'Vaccination', 'fr' => 'Vaccination', 'it' => 'Vaccinazione'],
                'description' => ['en' => 'Vaccines and health certificates', 'fr' => 'Vaccins et certificats de santé', 'it' => 'Vaccini e certificati sanitari'],
                'icon' => 'vaccines',
                'color' => '#4CAF50',
                'category_id' => 2
            ],
            [
                'name' => ['en' => 'Grooming', 'fr' => 'Toilettage', 'it' => 'Toelettatura'],
                'description' => ['en' => 'Complete grooming for pets', 'fr' => 'Toilettage complet pour animaux', 'it' => 'Toelettatura completa per animali'],
                'icon' => 'spa',
                'color' => '#E57C94',
                'category_id' => 3
            ],
            [
                'name' => ['en' => 'Pet-sitting', 'fr' => 'Garde d’animaux', 'it' => 'Pet-sitting'],
                'description' => ['en' => 'Animal care at home or host', 'fr' => 'Garde d’animaux à domicile ou en pension', 'it' => 'Cura degli animali a domicilio o in pensione'],
                'icon' => 'home',
                'color' => '#2196F3',
                'category_id' => 4
            ],
            [
                'name' => ['en' => 'Training', 'fr' => 'Dressage', 'it' => 'Addestramento'],
                'description' => ['en' => 'Behavioral and obedience training', 'fr' => 'Éducation comportementale et obéissance', 'it' => 'Educazione comportamentale e obbedienza'],
                'icon' => 'self-improvement',
                'color' => '#FFBE5E',
                'category_id' => 5
            ],
            [
                'name' => ['en' => 'Nutrition', 'fr' => 'Nutrition', 'it' => 'Nutrizione'],
                'description' => ['en' => 'Advice and food for pets', 'fr' => 'Conseils et alimentation pour animaux', 'it' => 'Consigli e alimenti per animali'],
                'icon' => 'restaurant',
                'color' => '#FFA500',
                'category_id' => 6
            ],
            [
                'name' => ['en' => 'Accessories', 'fr' => 'Accessoires', 'it' => 'Accessori'],
                'description' => ['en' => 'Toys, clothes and more', 'fr' => 'Jouets, vêtements et plus', 'it' => 'Giochi, abiti e altro'],
                'icon' => 'shopping-bag',
                'color' => '#795548',
                'category_id' => 7
            ],
            [
                'name' => ['en' => 'Adoption', 'fr' => 'Adoption', 'it' => 'Adozione'],
                'description' => ['en' => 'Find a pet to adopt', 'fr' => 'Trouvez un animal à adopter', 'it' => 'Trova un animale da adottare'],
                'icon' => 'favorite',
                'color' => '#F44336',
                'category_id' => 8
            ],
            [
                'name' => ['en' => 'Lost & Found', 'fr' => 'Perdus & Retrouvés', 'it' => 'Persi e trovati'],
                'description' => ['en' => 'Report lost or found animals', 'fr' => 'Signaler un animal perdu ou trouvé', 'it' => 'Segnala un animale perso o trovato'],
                'icon' => 'search',
                'color' => '#607D8B',
                'category_id' => 9
            ],
            [
                'name' => ['en' => 'Insurance', 'fr' => 'Assurance', 'it' => 'Assicurazione'],
                'description' => ['en' => 'Animal health insurance', 'fr' => 'Assurance santé pour animaux', 'it' => 'Assicurazione sanitaria per animali'],
                'icon' => 'verified',
                'color' => '#673AB7',
                'category_id' => 10
            ],
            [
                'name' => ['en' => 'Events', 'fr' => 'Événements', 'it' => 'Eventi'],
                'description' => ['en' => 'Pet-related events and meetups', 'fr' => 'Événements et rencontres pour animaux', 'it' => 'Eventi e incontri per animali'],
                'icon' => 'event',
                'color' => '#3F51B5',
                'category_id' => 11
            ],
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'icon' => $service['icon'],
                'color' => $service['color'],
                'active' => true,
                'price' => null,
                'category_id' => $service['category_id'],
            ]);
        }
    }
}
