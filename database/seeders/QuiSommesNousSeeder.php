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
                'description_fr' => "Soft Cactus est un réseau d’agences de communication 360° implanté à Casablanca depuis 2018 et à Oujda depuis 2021. Alliant conseil stratégique, branding, développement web, marketing digital et production audiovisuelle, nous accompagnons les entreprises dans la structuration de leur communication et le développement de leur marque. Chaque collaboration repose sur une compréhension approfondie des enjeux de nos clients et sur la conception de solutions sur mesure, adaptées à leurs objectifs et à leurs ambitions.",
                'description_en' => "Soft Cactus is a network of 360° communication agencies, established in Casablanca since 2018 and in Oujda since 2021. Combining strategic consulting, branding, web development, digital marketing and audiovisual production, we support companies in structuring their communication and developing their brand. Each collaboration is built on a deep understanding of our clients’ challenges and on designing tailor-made solutions adapted to their objectives and ambitions.",
                'description' => "Soft Cactus est un réseau d’agences de communication 360° implanté à Casablanca depuis 2018 et à Oujda depuis 2021. Alliant conseil stratégique, branding, développement web, marketing digital et production audiovisuelle, nous accompagnons les entreprises dans la structuration de leur communication et le développement de leur marque. Chaque collaboration repose sur une compréhension approfondie des enjeux de nos clients et sur la conception de solutions sur mesure, adaptées à leurs objectifs et à leurs ambitions.",
            ]
        );

        // Create the services
        $services = [
            [
                'title_fr' => 'DÉVELOPPEMENT WEB/MOBILE',
                'title_en' => 'WEB/MOBILE DEVELOPMENT',
                'text_fr' => 'Conception de sites vitrines, plateformes e-commerce et applications web et mobiles sur mesure, alliant design, performance et expérience utilisateur.',
                'text_en' => 'Design of custom showcase websites, e-commerce platforms and web and mobile applications, combining design, performance and user experience.',
                'sort_order' => 1,
            ],
            [
                'title_fr' => 'BRANDING & STRATÉGIE',
                'title_en' => 'BRANDING & STRATEGY',
                'text_fr' => 'Nous développons des stratégies de marque qui définissent votre identité, affirment votre positionnement et vous permettent de vous distinguer durablement de la concurrence.',
                'text_en' => 'We develop brand strategies that define your identity, affirm your positioning and enable you to stand durably apart from the competition.',
                'sort_order' => 2,
            ],
          
            [
                'title_fr' => 'Production audiovisuel & création de contenu',
                'title_en' => 'Audiovisual Production & Content Creation',
                'text_fr' => 'Nous assurons la gestion de vos réseaux sociaux de A à Z, de la planification éditoriale à la création, au montage et à la diffusion de contenus adaptés à votre marque et à vos objectifs.',
                'text_en' => 'We manage your social media from A to Z, from editorial planning to the creation, editing and distribution of content tailored to your brand and your objectives.',
                'sort_order' => 3,
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