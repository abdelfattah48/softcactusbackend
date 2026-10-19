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
                'lead_fr' => "Découvrez nos solutions digitales, pensées pour construire, développer et valoriser votre marque. De la Branding & Stratégie au Développement Web, en passant par le Marketing Digital et la Production Audiovisuelle, nous réunissons créativité, expertise et savoir-faire pour donner vie à des projets cohérents, impactants et adaptés à vos objectifs.",
                'lead_en' => "Discover our digital solutions, designed to build, grow and enhance your brand. From Branding & Strategy to Web Development, through Digital Marketing and Audiovisual Production, we bring together creativity, expertise and know-how to bring to life coherent, impactful projects tailored to your goals.",
                'lead'    => "Découvrez nos solutions digitales, pensées pour construire, développer et valoriser votre marque. De la Branding & Stratégie au Développement Web, en passant par le Marketing Digital et la Production Audiovisuelle, nous réunissons créativité, expertise et savoir-faire pour donner vie à des projets cohérents, impactants et adaptés à vos objectifs.",
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
                'text_fr'  => 'Nous construisons votre stratégie digitale pour renforcer votre visibilité, engager votre audience et générer des résultats. De la création de contenu au Social Media, Community Management et Sponsoring, chaque action est pensée pour répondre à vos objectifs.',
                'text_en'  => 'We build your digital strategy to strengthen your visibility, engage your audience and generate results. From content creation to Social Media, Community Management and Sponsoring, every action is designed to meet your goals.',
                'short'    => false,
            ],
            [
                'num'      => '02',
                'title_fr' => 'Développement Web',
                'title_en' => 'Web Development',
                'sub_fr'   => 'Transformer votre présence digitale.',
                'sub_en'   => 'Transform your digital presence.',
                'text_fr'  => 'Nous concevons des sites modernes, rapides et intuitifs, pensés pour valoriser votre image et convertir vos visiteurs en opportunités. De la conception UX/UI au développement sur mesure, nous créons des expériences digitales performantes, adaptées à votre activité et à vos objectifs.',
                'text_en'  => 'We design modern, fast and intuitive sites, built to enhance your image and turn your visitors into opportunities. From UX/UI design to custom development, we create high-performing digital experiences adapted to your business and goals.',
                'short'    => false,
            ],
            [
                'num'      => '03',
                'title_fr' => 'Branding',
                'title_en' => 'Branding',
                'sub_fr'   => 'Donner du sens à votre marque.',
                'sub_en'   => 'Give meaning to your brand.',
                'text_fr'  => 'Nous définissons votre positionnement, votre identité et votre stratégie pour construire une marque cohérente, reconnaissable et durable. Positionnement · Identité visuelle · Direction artistique · Stratégie de communication · Naming',
                'text_en'  => 'We define your positioning, identity and strategy to build a coherent, recognizable and lasting brand. Positioning · Visual identity · Art direction · Communication strategy · Naming',
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
