<?php

namespace Database\Seeders;

use App\Models\NotreHistoire;
use Illuminate\Database\Seeder;

class NotreHistoireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample history data
        $historyData = [
            [
                'year' => 2018,
                'description_fr' => 'Création de Soft Cactus à Casablanca. Début de notre aventure dans le digital avec une équipe de 3 personnes passionnées.',
                'description_en' => 'Creation of Soft Cactus in Casablanca. Beginning of our digital adventure with a team of 3 passionate people.',
                'sort_order' => 1,
            ],
            [
                'year' => 2019,
                'description_fr' => 'Première expansion avec l\'acquisition de 10 nouveaux clients et le développement de nos services web.',
                'description_en' => 'First expansion with the acquisition of 10 new clients and the development of our web services.',
                'sort_order' => 2,
            ],
            [
                'year' => 2020,
                'description_fr' => 'Adaptation rapide aux défis de la pandémie, développement de solutions digitales innovantes pour nos clients.',
                'description_en' => 'Quick adaptation to pandemic challenges, development of innovative digital solutions for our clients.',
                'sort_order' => 3,
            ],
            [
                'year' => 2021,
                'description_fr' => 'Ouverture de notre deuxième agence à Oujda, expansion de notre équipe à 15 collaborateurs.',
                'description_en' => 'Opening of our second agency in Oujda, expansion of our team to 15 collaborators.',
                'sort_order' => 4,
            ],
            [
                'year' => 2022,
                'description_fr' => 'Lancement de nos services de production audiovisuelle et renforcement de notre expertise en branding.',
                'description_en' => 'Launch of our audiovisual production services and strengthening of our branding expertise.',
                'sort_order' => 5,
            ],
            [
                'year' => 2023,
                'description_fr' => 'Certification ISO et reconnaissance comme agence leader dans la région. Plus de 100 projets réalisés.',
                'description_en' => 'ISO certification and recognition as a leading agency in the region. More than 100 projects completed.',
                'sort_order' => 6,
            ],
        ];

        foreach ($historyData as $data) {
            NotreHistoire::updateOrCreate(
                ['year' => $data['year']],
                [
                    'description' => $data['description_fr'], // Default fallback
                    'description_fr' => $data['description_fr'],
                    'description_en' => $data['description_en'],
                    'enabled' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }

        $this->command->info('✅ Notre Histoire data seeded successfully!');
    }
}