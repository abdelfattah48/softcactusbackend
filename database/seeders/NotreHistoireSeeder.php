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
        // History data from ourstory.jsx component - actual company timeline
        $historyData = [
            [
                'year' => 2018,
                'description_fr' => 'Naissance de Soft Cactus et début de l\'aventure entrepreneuriale de sa fondatrice HANAE DEKHISSI.',
                'description_en' => 'Birth of Soft Cactus and the beginning of the entrepreneurial adventure of its founder HANAE DEKHISSI.',
                'sort_order' => 1,
            ],
            [
                'year' => 2019,
                'description_fr' => 'Soft Cactus ouvre ses portes, à Casablanca, au Abdelmoumen, Maroc.',
                'description_en' => 'Soft Cactus opens its doors in Casablanca, at Abdelmoumen, Morocco.',
                'sort_order' => 2,
            ],
            [
                'year' => 2020,
                'description_fr' => 'Ce qui nuit à l\'un, nuit a l\'autre. La crise pandémique était une nouvelle chance pour que nous puissions nous démarquer dans le domaine digital.',
                'description_en' => 'What harms one, harms the other. The pandemic crisis was a new opportunity for us to stand out in the digital field.',
                'sort_order' => 3,
            ],
            [
                'year' => 2021,
                'description_fr' => 'Rebelote, mais avec un nouvel esprit et une deuxième agence à Oujda.',
                'description_en' => 'Here we go again, but with a new spirit and a second agency in Oujda.',
                'sort_order' => 4,
            ],
            [
                'year' => 2022,
                'description_fr' => 'Renforcement de ce projet ambitieux à l\'Oriental, à travers le programme Forsa "Opportunité".',
                'description_en' => 'Strengthening this ambitious project in the East, through the Forsa "Opportunity" program.',
                'sort_order' => 5,
            ],
            [
                'year' => 2023,
                'description_fr' => 'Finalement tous réunis à l\'agence, tout en ayant des clients partout dans le Maroc.',
                'description_en' => 'Finally all together at the agency, while having clients throughout Morocco.',
                'sort_order' => 6,
            ],
            [
                'year' => 2024,
                'description_fr' => 'Une année d\'audace, où chaque défi est devenu une opportunité de croissance et chaque idée a trouvé son terrain d\'expression.',
                'description_en' => 'A year of boldness, where every challenge became an opportunity for growth and every idea found its ground for expression.',
                'sort_order' => 7,
            ],
            [
                'year' => 2025,
                'description_fr' => 'Une année marquée par la fidélité de nos clients, la force de notre équipe et une créativité qui n\'a jamais cessé d\'évoluer.',
                'description_en' => 'A year marked by the loyalty of our clients, the strength of our team and creativity that never stopped evolving.',
                'sort_order' => 8,
            ],
            [
                'year' => 2026,
                'description_fr' => 'L\'année de l\'innovation et de l\'expansion, où Soft Cactus continue de repousser les limites du possible dans le domaine digital.',
                'description_en' => 'The year of innovation and expansion, where Soft Cactus continues to push the boundaries of what\'s possible in the digital field.',
                'sort_order' => 9,
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