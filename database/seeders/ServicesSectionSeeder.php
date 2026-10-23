<?php

namespace Database\Seeders;

use App\Models\ServicesSectionSetting;
use App\Models\ServiceStep;
use App\Models\ServiceCard;
use Illuminate\Database\Seeder;

class ServicesSectionSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Settings (intro / approach / button) -----------------------------
        ServicesSectionSetting::updateOrCreate(
            ['id' => 1],
            [
                'intro_prefix_fr'    => 'Découvrez',
                'intro_prefix_en'    => 'Discover',
                'intro_prefix'       => 'Découvrez',
                'intro_highlight_fr' => 'nos solutions digitales',
                'intro_highlight_en' => 'our digital solutions',
                'intro_highlight'    => 'nos solutions digitales',
                'lead_fr' => "Découvrez nos expertises en communication 360°, conçues pour structurer votre image de marque, renforcer votre présence digitale et soutenir votre croissance. De la stratégie de marque au développement web, en passant par le marketing digital et la production audiovisuelle, nous combinons créativité et expertise pour concevoir des solutions sur mesure, alignées sur vos objectifs.",
                'lead_en' => "Discover our 360° communication expertise, designed to structure your brand image, strengthen your digital presence and support your growth. From brand strategy to web development, through digital marketing and audiovisual production, we combine creativity and expertise to design tailor-made solutions aligned with your goals.",
                'lead'    => "Découvrez nos expertises en communication 360°, conçues pour structurer votre image de marque, renforcer votre présence digitale et soutenir votre croissance. De la stratégie de marque au développement web, en passant par le marketing digital et la production audiovisuelle, nous combinons créativité et expertise pour concevoir des solutions sur mesure, alignées sur vos objectifs.",
                'approach_title_fr' => 'Une approche. Quatre expertises.',
                'approach_title_en' => 'One approach. Four expertises.',
                'approach_title'    => 'Une approche. Quatre expertises.',
                'button_label_fr'   => 'Demandez un audit stratégique',
                'button_label_en'   => 'Request a strategic audit',
                'button_label'      => 'Demandez un audit stratégique',
            ]
        );

        // ---- Approach steps ---------------------------------------------------
        $steps = [
            [
                'title_fr' => 'STRATÉGIE',
                'title_en' => 'STRATEGY',
                'text_fr'  => 'Nous commençons par comprendre votre marque.',
                'text_en'  => 'We start by understanding your brand.',
            ],
            [
                'title_fr' => 'CRÉATION',
                'title_en' => 'CREATION',
                'text_fr'  => 'Nous transformons les idées en identités et expériences fortes.',
                'text_en'  => 'We turn ideas into strong identities and experiences.',
            ],
            [
                'title_fr' => 'DÉPLOIEMENT',
                'title_en' => 'DEPLOYMENT',
                'text_fr'  => 'Nous transformons les idées en identités et expériences fortes.',
                'text_en'  => 'We turn ideas into strong identities and experiences.',
            ],
            [
                'title_fr' => 'CROISSANCE',
                'title_en' => 'GROWTH',
                'text_fr'  => 'Nous mesurons, optimisons et construisons sur le long terme.',
                'text_en'  => 'We measure, optimize and build for the long term.',
            ],
        ];

        foreach ($steps as $i => $step) {
            ServiceStep::updateOrCreate(
                ['title_fr' => $step['title_fr']],
                [
                    'title'      => $step['title_fr'],
                    'title_fr'   => $step['title_fr'],
                    'title_en'   => $step['title_en'],
                    'text'       => $step['text_fr'],
                    'text_fr'    => $step['text_fr'],
                    'text_en'    => $step['text_en'],
                    'enabled'    => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        // ---- Service cards ----------------------------------------------------
        $cards = [
            [
                'num'      => '01',
                'title_fr' => 'Création de contenu',
                'title_en' => 'Content creation',
                'sub_fr'   => 'Faire grandir votre présence.',
                'sub_en'   => 'Grow your presence.',
                'text_fr'  => 'Nous élaborons des stratégies de marketing digital adaptées à vos objectifs pour développer votre visibilité, renforcer l’engagement de votre audience et générer des opportunités commerciales. De la création de contenu à la gestion des réseaux sociaux, en passant par le community management et les campagnes sponsorisées, chaque action s’inscrit dans une stratégie cohérente et orientée résultats.',
                'text_en'  => 'We develop digital marketing strategies tailored to your goals to grow your visibility, strengthen your audience engagement and generate business opportunities. From content creation to social media management, through community management and sponsored campaigns, every action is part of a coherent, results-driven strategy.',
                'short'    => false,
            ],
            [
                'num'      => '02',
                'title_fr' => 'Développement Web',
                'title_en' => 'Web Development',
                'sub_fr'   => 'Transformer votre présence digitale.',
                'sub_en'   => 'Transform your digital presence.',
                'text_fr'  => 'Nous développons des sites web performants, intuitifs et adaptés à votre activité pour renforcer votre présence digitale et transformer vos visiteurs en clients. De la conception UX/UI au développement sur mesure, chaque projet allie design, fonctionnalité et performance pour répondre à vos objectifs commerciaux.',
                'text_en'  => 'We develop high-performing, intuitive websites tailored to your business to strengthen your digital presence and turn your visitors into clients. From UX/UI design to custom development, every project combines design, functionality and performance to meet your business goals.',
                'short'    => false,
            ],
            [
                'num'      => '03',
                'title_fr' => 'Branding',
                'title_en' => 'Branding',
                'sub_fr'   => 'Donner du sens à votre marque.',
                'sub_en'   => 'Give meaning to your brand.',
                'text_fr'  => 'Nous construisons des marques fortes et distinctives en définissant leur positionnement, leur identité et leur stratégie de communication. Chaque élément est pensé pour assurer une image cohérente, renforcer votre différenciation et inscrire votre marque dans la durée. Positionnement · Identité visuelle · Direction artistique · Stratégie de communication · Naming',
                'text_en'  => 'We build strong, distinctive brands by defining their positioning, identity and communication strategy. Every element is designed to ensure a coherent image, strengthen your differentiation and establish your brand for the long term. Positioning · Visual identity · Art direction · Communication strategy · Naming',
                'short'    => true,
            ],
        ];

        foreach ($cards as $i => $card) {
            ServiceCard::updateOrCreate(
                ['num' => $card['num']],
                [
                    'num'        => $card['num'],
                    'title'      => $card['title_fr'],
                    'title_fr'   => $card['title_fr'],
                    'title_en'   => $card['title_en'],
                    'sub'        => $card['sub_fr'],
                    'sub_fr'     => $card['sub_fr'],
                    'sub_en'     => $card['sub_en'],
                    'text'       => $card['text_fr'],
                    'text_fr'    => $card['text_fr'],
                    'text_en'    => $card['text_en'],
                    'short'      => $card['short'],
                    'enabled'    => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $this->command->info('✅ Services section data seeded successfully!');
    }
}
