<?php

namespace Database\Seeders;

use App\Models\QuiSommesNousSetting;
use App\Models\QuiSommesNousService;
use Illuminate\Database\Seeder;

class QuiSommesNousSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update the main settings
        QuiSommesNousSetting::updateOrCreate(
            ['id' => 1],
            [
                'description_fr' => "Soft Cactus est un réseau d'agences de communication digitale, implanté à Casablanca depuis 2018 et à Oujda depuis 2021. Spécialisée en marketing digital avec un fort accent sur le conseil. Soft Cactus accompagne ses clients à chaque étape de leur projet en comprenant leurs besoins et en proposant des solutions sur mesure.",
                'description_en' => "Soft Cactus is a network of digital communication agencies, established in Casablanca since 2018 and in Oujda since 2021. Specialized in digital marketing with a strong focus on consulting. Soft Cactus supports its clients at every stage of their project by understanding their needs and offering tailor-made solutions.",
                'description' => "Soft Cactus est un réseau d'agences de communication digitale, implanté à Casablanca depuis 2018 et à Oujda depuis 2021. Spécialisée en marketing digital avec un fort accent sur le conseil. Soft Cactus accompagne ses clients à chaque étape de leur projet en comprenant leurs besoins et en proposant des solutions sur mesure.",
            ]
        );

        // Create the services
        $services = [
            [
                'title_fr' => 'DÉVELOPPEMENT WEB/MOBILE',
                'title_en' => 'WEB/MOBILE DEVELOPMENT',
                'text_fr' => 'Conception de sites web vitrine, e-commerce, ainsi que d\'applications web et mobiles. Vidéos promotionnelles : Réalisation de vidéos attractives et engageantes pour mettre en valeur votre activité.',
                'text_en' => 'Design of showcase websites, e-commerce, as well as web and mobile applications. Promotional videos: Creation of attractive and engaging videos to showcase your business.',
                'sort_order' => 1,
            ],
            [
                'title_fr' => 'BRANDING & STRATÉGIE',
                'title_en' => 'BRANDING & STRATEGY',
                'text_fr' => 'Nous construisons des stratégies de marque qui révèlent votre identité, renforcent votre positionnement et valorisent votre entreprise sur un marché concurrentiel.',
                'text_en' => 'We build brand strategies that reveal your identity, strengthen your positioning and enhance your company in a competitive market.',
                'sort_order' => 2,
            ],
            [
                'title_fr' => 'MARKETING DIGITAL',
                'title_en' => 'DIGITAL MARKETING',
                'text_fr' => 'Gestion complète des réseaux sociaux, incluant la planification, la production, le montage et la diffusion des contenus.',
                'text_en' => 'Complete management of social networks, including planning, production, editing and content distribution.',
                'sort_order' => 3,
            ],
            [
                'title_fr' => 'PRODUCTION AUDIOVISUELLE',
                'title_en' => 'AUDIOVISUAL PRODUCTION',
                'text_fr' => 'Services complets de production audiovisuelle, du concept à la réalisation finale, incluant tournages, montage et post-production.',
                'text_en' => 'Complete audiovisual production services, from concept to final realization, including filming, editing and post-production.',
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $serviceData) {
            QuiSommesNousService::updateOrCreate(
                ['title_fr' => $serviceData['title_fr']],
                [
                    'title' => $serviceData['title_fr'], // Default fallback
                    'title_fr' => $serviceData['title_fr'],
                    'title_en' => $serviceData['title_en'],
                    'text' => $serviceData['text_fr'], // Default fallback
                    'text_fr' => $serviceData['text_fr'],
                    'text_en' => $serviceData['text_en'],
                    'enabled' => true,
                    'sort_order' => $serviceData['sort_order'],
                ]
            );
        }

        $this->command->info('✅ Qui Sommes-Nous data seeded successfully!');
    }
}