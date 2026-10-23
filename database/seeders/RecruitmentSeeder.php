<?php

namespace Database\Seeders;

use App\Models\RecruitmentSetting;
use App\Models\RecruitmentJobOffer;
use App\Models\RecruitmentTalent;
use App\Models\RecruitmentWhyCard;
use Illuminate\Database\Seeder;

class RecruitmentSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Settings (hero + section titles + CTA) ---------------------------
        RecruitmentSetting::updateOrCreate(
            ['id' => 1],
            [
                'hero_title_fr' => 'Et si votre prochain défi commençait ici ?',
                'hero_title_en' => 'What if your next challenge started here?',
                'hero_title'    => 'Et si votre prochain défi commençait ici ?',

                'hero_paragraph_fr' => "Chez Soft Cactus, nous combinons stratégie, créativité et expertise pour donner vie à des projets qui renforcent les marques. Notre équipe d’experts en branding, digital, développement web et production audiovisuelle conçoit des solutions cohérentes, pensées pour vous démarquer et soutenir votre croissance.",
                'hero_paragraph_en' => 'At Soft Cactus, we combine strategy, creativity and expertise to bring to life projects that strengthen brands. Our team of experts in branding, digital, web development and audiovisual production designs coherent solutions, crafted to set you apart and support your growth.',
                'hero_paragraph'    => "Chez Soft Cactus, nous combinons stratégie, créativité et expertise pour donner vie à des projets qui renforcent les marques. Notre équipe d’experts en branding, digital, développement web et production audiovisuelle conçoit des solutions cohérentes, pensées pour vous démarquer et soutenir votre croissance.",

                'hero_lead_fr' => "Envie de faire partie de l’aventure ?",
                'hero_lead_en' => 'Want to be part of the adventure?',
                'hero_lead'    => "Envie de faire partie de l’aventure ?",

                'hero_cta_fr' => 'Découvrir nos opportunités · Candidature spontanée',
                'hero_cta_en' => 'Discover our opportunities · Spontaneous application',
                'hero_cta'    => 'Découvrir nos opportunités · Candidature spontanée',

                'jobs_title_first_fr' => 'Nos',
                'jobs_title_first_en' => 'Our',
                'jobs_title_first'    => 'Nos',
                'jobs_title_highlight_fr' => 'opportunités',
                'jobs_title_highlight_en' => 'opportunities',
                'jobs_title_highlight'    => 'opportunités',

                'talents_title_first_fr' => 'Nos stagiaires — La nouvelle',
                'talents_title_first_en' => 'Our interns — The new',
                'talents_title_first'    => 'Nos stagiaires — La nouvelle',
                'talents_title_highlight_fr' => 'génération de talents',
                'talents_title_highlight_en' => 'generation of talents',
                'talents_title_highlight'    => 'génération de talents',

                'talents_box_title_fr' => 'Nos futurs talents',
                'talents_box_title_en' => 'Our future talents',
                'talents_box_title'    => 'Nos futurs talents',

                'talents_box_text_fr' => "Vos valeurs reflètent votre marque. Notre équipe croit en compétences. C’est notre culture organisationnelle. Soft cactus opte pour une culture de personnalité, soft skills et comportement avant, parcours et formations.",
                'talents_box_text_en' => 'Your values reflect your brand. Our team believes in skills. This is our organizational culture. Soft Cactus opts for a culture of personality, soft skills and behavior first, background and training.',
                'talents_box_text'    => "Vos valeurs reflètent votre marque. Notre équipe croit en compétences. C’est notre culture organisationnelle. Soft cactus opte pour une culture de personnalité, soft skills et comportement avant, parcours et formations.",

                'talents_side_text_fr' => "Les talents d’aujourd’hui, Les créatifs de demain.",
                'talents_side_text_en' => "Today's talents, tomorrow's creatives.",
                'talents_side_text'    => "Les talents d’aujourd’hui, Les créatifs de demain.",

                'why_title_first_fr' => 'Pourquoi',
                'why_title_first_en' => 'Why',
                'why_title_first'    => 'Pourquoi',
                'why_title_highlight_fr' => 'nous rejoindre ?',
                'why_title_highlight_en' => 'join us?',
                'why_title_highlight'    => 'nous rejoindre ?',

                'cta_title_fr' => 'Votre talent a sa place chez nous.',
                'cta_title_en' => 'Your talent has a place with us.',
                'cta_title'    => 'Votre talent a sa place chez nous.',

                'cta_text_fr' => "Vous êtes curieux, créatif et motivé ? Que vous recherchiez un stage ou une nouvelle opportunité professionnelle, Soft Cactus est prêt à découvrir votre potentiel.",
                'cta_text_en' => 'Are you curious, creative and motivated? Whether you are looking for an internship or a new professional opportunity, Soft Cactus is ready to discover your potential.',
                'cta_text'    => "Vous êtes curieux, créatif et motivé ? Que vous recherchiez un stage ou une nouvelle opportunité professionnelle, Soft Cactus est prêt à découvrir votre potentiel.",

                'cta_button_fr' => 'Postuler chez SOFT CACTUS',
                'cta_button_en' => 'Apply at SOFT CACTUS',
                'cta_button'    => 'Postuler chez SOFT CACTUS',
            ]
        );

        // ---- Job offers ("Nos opportunités") -----------------------------------
        $jobs = [
            [
                'num' => '01',
                'title_fr' => 'Graphic designer',
                'title_en' => 'Graphic designer',
                'description_fr' => 'Concevez des identités visuelles fortes et donnez vie à vos idées créatives.',
                'description_en' => 'Design strong visual identities and bring your creative ideas to life.',
            ],
            [
                'num' => '02',
                'title_fr' => 'Community Management',
                'title_en' => 'Community Management',
                'description_fr' => 'Stratégie digitale, gestion des réseaux sociaux et création de contenu engageant.',
                'description_en' => 'Digital strategy, social media management and engaging content creation.',
            ],
            [
                'num' => '03',
                'title_fr' => 'Développeur Web',
                'title_en' => 'Web Developer',
                'description_fr' => 'Sites web, expérience utilisateur, développement digital et solutions sur mesure.',
                'description_en' => 'Websites, user experience, digital development and tailored solutions.',
            ],
            [
                'num' => '04',
                'title_fr' => 'Videographer & Video Editor',
                'title_en' => 'Videographer & Video Editor',
                'description_fr' => 'Production audiovisuelle, tournage, montage vidéo et création de contenu.',
                'description_en' => 'Audiovisual production, filming, video editing and content creation.',
            ],
        ];

        foreach ($jobs as $i => $job) {
            RecruitmentJobOffer::updateOrCreate(
                ['num' => $job['num'], 'title_fr' => $job['title_fr']],
                [
                    'title'          => $job['title_fr'],
                    'title_fr'       => $job['title_fr'],
                    'title_en'       => $job['title_en'],
                    'description'    => $job['description_fr'],
                    'description_fr' => $job['description_fr'],
                    'description_en' => $job['description_en'],
                    'enabled'        => true,
                    'sort_order'     => $i + 1,
                ]
            );
        }

        // ---- Talents (stagiaires) ----------------------------------------------
        $talents = [
            [
                'first_name' => 'Abdelfattah',
                'last_name'  => 'BEKKAL',
                'role_fr'    => 'Étudiant en ingénierie des systèmes d’information en 4ᵉ année - SUPMTI',
                'role_en'    => '4th year Information Systems Engineering student - SUPMTI',
                'image'      => '/assets/images/recrutement/stagiaire.jpg',
            ],
            [
                'first_name' => 'Yasmine',
                'last_name'  => 'EL AMRANI',
                'role_fr'    => 'Étudiante en design graphique & UX/UI en 3ᵉ année - HEAC',
                'role_en'    => '3rd year Graphic Design & UX/UI student - HEAC',
                'image'      => '/assets/images/drmoustaid%20images/profileimage.svg',
            ],
        ];

        foreach ($talents as $i => $t) {
            RecruitmentTalent::updateOrCreate(
                ['first_name' => $t['first_name'], 'last_name' => $t['last_name']],
                [
                    'role'       => $t['role_fr'],
                    'role_fr'    => $t['role_fr'],
                    'role_en'    => $t['role_en'],
                    'image'      => $t['image'],
                    'enabled'    => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        // ---- Why cards ("Pourquoi nous rejoindre") ------------------------------
        $whys = [
            [
                'num' => '01',
                'title_fr' => 'Libérez votre créativité',
                'title_en' => 'Unleash your creativity',
                'text_fr'  => 'Exprimez vos idées et participez à des projets qui donnent vie aux marques.',
                'text_en'  => 'Express your ideas and take part in projects that bring brands to life.',
            ],
            [
                'num' => '02',
                'title_fr' => 'Évoluez avec nous',
                'title_en' => 'Grow with us',
                'text_fr'  => 'Développez vos talents auprès de professionnels passionnés.',
                'text_en'  => 'Develop your talents alongside passionate professionals.',
            ],
            [
                'num' => '03',
                'title_fr' => 'Grandissez en équipe',
                'title_en' => 'Grow as a team',
                'text_fr'  => 'Partagez vos idées, collaborez et contribuez à une vision commune.',
                'text_en'  => 'Share your ideas, collaborate and contribute to a common vision.',
            ],
            [
                'num' => '04',
                'title_fr' => 'Passez à l’action',
                'title_en' => 'Take action',
                'text_fr'  => 'Participez à des projets concrets et développez vos talents.',
                'text_en'  => 'Take part in concrete projects and develop your talents.',
            ],
        ];

        foreach ($whys as $i => $w) {
            RecruitmentWhyCard::updateOrCreate(
                ['num' => $w['num'], 'title_fr' => $w['title_fr']],
                [
                    'title'    => $w['title_fr'],
                    'title_fr' => $w['title_fr'],
                    'title_en' => $w['title_en'],
                    'text'     => $w['text_fr'],
                    'text_fr'  => $w['text_fr'],
                    'text_en'  => $w['text_en'],
                    'enabled'  => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $this->command->info('✅ Recruitment page data seeded successfully!');
    }
}
