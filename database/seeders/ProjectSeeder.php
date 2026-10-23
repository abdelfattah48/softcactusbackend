<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Seeds the 8 real portfolio projects.
     * Cover cards come from public/projectcovercards/*.
     * Idempotent: matches projects by public_card.title_fr and updates them.
     *
     * NOTE: category_en intentionally equals the French category name because
     * the website filter builds its chips from Category.name_fr in BOTH languages.
     */
    public function run(): void
    {
        $projects = [
            $this->medicalMoustaideProject(),
            $this->medicalIbnRochdProject(),
            $this->agroalimentaireInterEpicesProject(),
            $this->meuniereElGhazyProject(),
            $this->immobilierPrestaciaProject(),
            $this->immobilierMaxsamProject(),
            $this->tourismeOasisBeachProject(),
            $this->hotelleriePalaisDorientProject(),
        ];

        $existing = Project::all();

        foreach ($projects as $project) {
            $title = $project['public_card']['title_fr'] ?? null;

            $match = $existing->first(function ($item) use ($title) {
                return is_array($item->public_card ?? null)
                    && ($item->public_card['title_fr'] ?? null) === $title;
            });

            if ($match) {
                $match->update($project);
            } else {
                Project::create($project);
            }
        }
    }

    /**
     * Build one project payload.
     */
    private function project(
        string $coverFile,
        string $titleFr,
        string $titleEn,
        string $category,
        string $shortDescriptionFr,
        string $shortDescriptionEn,
        array $details
    ): array {
        return [
            'status' => 'published',
            'public_card' => [
                'image' => [
                    'url' => url('/projectcovercards/' . $coverFile),
                    'alt' => $titleFr,
                ],
                'title_fr' => $titleFr,
                'title_en' => $titleEn,
                'category_fr' => $category,
                'category_en' => $category,
                'shortDescription_fr' => $shortDescriptionFr,
                'shortDescription_en' => $shortDescriptionEn,
                'tags' => [$category],
                'featured' => false,
                'visible' => true,
                'order' => 0,
                'ctaText' => 'View Project',
                'metadata' => '',
            ],
            'details' => $details,
        ];
    }

    /**
     * Simple bilingual service row for servicesOffered.
     */
    private function service(string $titleFr, string $titleEn, string $descFr, string $descEn): array
    {
        return [
            'title_fr' => $titleFr,
            'title_en' => $titleEn,
            'description_fr' => $descFr,
            'description_en' => $descEn,
        ];
    }

    /**
     * One carousel card (image required by the backoffice UI).
     */
    private function carouselCard(string $coverFile, string $titleFr, string $titleEn, string $subtitleFr, string $subtitleEn): array
    {
        return [
            'image' => [
                'url' => url('/projectcovercards/' . $coverFile),
                'alt' => $titleFr,
            ],
            'title_fr' => $titleFr,
            'title_en' => $titleEn,
            'subtitle_fr' => $subtitleFr,
            'subtitle_en' => $subtitleEn,
        ];
    }

    // =====================================================
    // 1 — MÉDICAL : Cabinet de Dermatologie Dr. Kaoutar Moustaide
    // (real content, mirrors the static projectDetails in the website)
    // =====================================================
    private function medicalMoustaideProject(): array
    {
        return $this->project(
            'drmoustaid.jpg',
            'Cabinet de Dermatologie — Dr. Kaoutar Moustaide',
            'Dermatology Clinic — Dr. Kaoutar Moustaide',
            'Médical',
            'Site vitrine, branding et gestion des réseaux sociaux pour un cabinet de dermatologie à Nador.',
            'Showcase website, branding and social media management for a dermatology clinic in Nador.',
            [
                'description_fr' => 'Dr. Kaoutar MOUSTAIDE, cabinet de dermatologie à NADOR, vous accueille pour une prise en charge complète des maladies de la peau, des cheveux et des ongles, ainsi que des soins de médecine esthétique et de traitements laser. Grâce à une approche personnalisée et des technologies de pointe, elle s\'engage à offrir des soins de qualité dans un environnement moderne, sécurisé et à l\'écoute de chaque patient.',
                'description_en' => 'Dr. Kaoutar MOUSTAIDE, a dermatology clinic in NADOR, welcomes you for comprehensive care of skin, hair, and nail conditions, as well as aesthetic medicine treatments and laser therapy. With a personalized approach and cutting-edge technologies, she is committed to providing quality care in a modern, secure environment that listens to each patient.',
                'duration_fr' => 'Une collaboration de 7 mois avec le Dr Kaoutar Moustaide.',
                'duration_en' => 'A 7-month collaboration with Dr. Kaoutar Moustaide.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site présentant la maquette sur mesure, responsive et optimisé',
                        'Custom showcase website, responsive and optimized'
                    ),
                    $this->service(
                        'Branding', 'Branding',
                        'Logo, charte graphique, papeterie',
                        'Logo, brand guidelines, stationery'
                    ),
                    $this->service(
                        'Consulting', 'Consulting',
                        'Un conseil stratégique au service de la croissance de votre entreprise.',
                        'Strategic advice serving the growth of your business.'
                    ),
                    $this->service(
                        'Community management', 'Community management',
                        'Audit, stratégie de communication, tournage, gestion des réseaux sociaux.',
                        'Audit, communication strategy, filming, social media management.'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('drmoustaid.jpg', 'DÉVELOPPEMENT WEB', 'WEB DEVELOPMENT', 'Site Présentatif', 'Showcase Website'),
                    $this->carouselCard('drmoustaid.jpg', 'BRANDING', 'BRANDING', 'Logo Design', 'Logo Design'),
                    $this->carouselCard('drmoustaid.jpg', 'UI / UX DESIGN', 'UI / UX DESIGN', 'Mobile App', 'Mobile App'),
                    $this->carouselCard('drmoustaid.jpg', 'SEO', 'SEO', 'Marketing', 'Marketing'),
                ],
                'resultsContent' => [
                    'title_fr' => 'Résultats Social Media',
                    'title_en' => 'Social Media Results',
                    'subtitle_fr' => 'Des stratégies créatives qui génèrent une croissance mesurable et un engagement durable.',
                    'subtitle_en' => 'Creative strategies that generate measurable growth and lasting engagement.',
                    'tags_fr' => 'Instagram, Facebook, Tiktok',
                    'tags_en' => 'Instagram, Facebook, Tiktok',
                    'profileImage' => null,
                    'profileName_fr' => 'Dr. Kaoutar Moustaide',
                    'profileName_en' => 'Dr. Kaoutar Moustaide',
                    'profileRole_fr' => 'Dermatologue',
                    'profileRole_en' => 'Dermatologist',
                    'stats' => [
                        [
                            'platform' => 'instagram',
                            'views' => '2.5',
                            'viewsSuffix_fr' => 'M+ Vues',
                            'viewsSuffix_en' => 'M+ Views',
                            'subs' => '35',
                            'subsSuffix_fr' => 'K+ Abonnés',
                            'subsSuffix_en' => 'K+ Followers',
                        ],
                    ],
                    'carouselImages' => [],
                ],
            ]
        );
    }

    // =====================================================
    // 2 — MÉDICAL : Clinique Ibn Rochd
    // =====================================================
    private function medicalIbnRochdProject(): array
    {
        return $this->project(
            'ibnrochd.png',
            'Clinique Ibn Rochd',
            'Ibn Rochd Clinic',
            'Médical',
            'Refonte digitale complète pour une clinique multi-spécialités : site vitrine, réservation et communication.',
            'Complete digital redesign for a multi-specialty clinic: showcase website, booking and communication.',
            [
                'description_fr' => 'La Clinique Ibn Rochd offre une prise en charge médicale pluridisciplinaire avec des équipements de pointe. Nous avons accompagné sa transformation digitale : identité visuelle, site web professionnel avec prise de rendez-vous, et présence sur les réseaux sociaux pour rassurer et rapprocher les patients.',
                'description_en' => 'Ibn Rochd Clinic offers multidisciplinary medical care with state-of-the-art equipment. We supported its digital transformation: visual identity, a professional website with appointment booking, and a social media presence to build patient trust and closeness.',
                'duration_fr' => 'Une collaboration de 6 mois avec la Clinique Ibn Rochd.',
                'duration_en' => 'A 6-month collaboration with Ibn Rochd Clinic.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site vitrine avec prise de rendez-vous en ligne',
                        'Showcase website with online appointment booking'
                    ),
                    $this->service(
                        'Branding', 'Branding',
                        'Identité visuelle et signalétique',
                        'Visual identity and signage'
                    ),
                    $this->service(
                        'Community management', 'Community management',
                        'Stratégie de contenu santé et animation des réseaux sociaux',
                        'Health content strategy and social media management'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('ibnrochd.png', 'DÉVELOPPEMENT WEB', 'WEB DEVELOPMENT', 'Site avec Rendez-vous', 'Booking Website'),
                    $this->carouselCard('ibnrochd.png', 'BRANDING', 'BRANDING', 'Identité Visuelle', 'Visual Identity'),
                ],
            ]
        );
    }

    // =====================================================
    // 3 — AGROALIMENTAIRE : Inter Épices
    // =====================================================
    private function agroalimentaireInterEpicesProject(): array
    {
        return $this->project(
            'interepices.jpg',
            'Inter Épices',
            'Inter Epices',
            'Agroalimentaire',
            'Image de marque et packaging pour une marque d\'épices premium.',
            'Brand identity and packaging for a premium spices brand.',
            [
                'description_fr' => 'Inter Épices sélectionne et conditionne des épices d\'exception. Notre mission : construire une marque forte et crédible — logo, charte graphique, packaging produit et supports de vente — pour conquérir aussi bien les épiceries fines que les professionnels de la restauration.',
                'description_en' => 'Inter Epices selects and packs exceptional spices. Our mission: build a strong, credible brand — logo, brand guidelines, product packaging and sales collateral — to win over both gourmet grocery stores and food-service professionals.',
                'duration_fr' => 'Une collaboration de 4 mois avec Inter Épices.',
                'duration_en' => 'A 4-month collaboration with Inter Epices.',
                'servicesOffered' => [
                    $this->service(
                        'Branding', 'Branding',
                        'Création du logo et de la charte graphique',
                        'Logo creation and brand guidelines'
                    ),
                    $this->service(
                        'Design packaging', 'Packaging design',
                        'Étiquettes et packings de la gamme d\'épices',
                        'Labels and packaging for the spices range'
                    ),
                    $this->service(
                        'Marketing Digital', 'Digital Marketing',
                        'Lancement et animation des réseaux sociaux',
                        'Social media launch and management'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('interepices.jpg', 'BRANDING', 'BRANDING', 'Logo & Charte', 'Logo & Guidelines'),
                    $this->carouselCard('interepices.jpg', 'PACKAGING', 'PACKAGING', 'Gamme Épices', 'Spices Range'),
                ],
            ]
        );
    }

    // =====================================================
    // 4 — INDUSTRIE MEUNIÈRE : Groupe El Ghazy
    // =====================================================
    private function meuniereElGhazyProject(): array
    {
        return $this->project(
            'elghazy.jpg',
            'Groupe El Ghazy',
            'El Ghazy Group',
            'Industrie meunière',
            'Présence digitale institutionnelle pour un acteur industriel de référence.',
            'Corporate digital presence for a leading industrial player.',
            [
                'description_fr' => 'Le Groupe El Ghazy est un acteur majeur de l\'industrie meunière. Nous avons conçu sa plateforme digitale institutionnelle : présentation du groupe, de ses capacités industrielles et de ses engagements qualité, avec une expérience utilisateur à la hauteur de sa réputation.',
                'description_en' => 'El Ghazy Group is a major player in the flour milling industry. We designed its corporate digital platform: group presentation, industrial capabilities and quality commitments, with a user experience matching its reputation.',
                'duration_fr' => 'Une collaboration de 5 mois avec le Groupe El Ghazy.',
                'duration_en' => 'A 5-month collaboration with El Ghazy Group.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site institutionnel multi-langues',
                        'Multi-language corporate website'
                    ),
                    $this->service(
                        'Design UI / UX', 'UI / UX Design',
                        'Parcours de visite et vitrine des métiers',
                        'Visitor journeys and business showcase'
                    ),
                    $this->service(
                        'SEO', 'SEO',
                        'Optimisation pour les recherches professionnelles',
                        'Optimized for professional search queries'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('elghazy.jpg', 'DÉVELOPPEMENT WEB', 'WEB DEVELOPMENT', 'Site Institutionnel', 'Corporate Website'),
                    $this->carouselCard('elghazy.jpg', 'SEO', 'SEO', 'Visibilité Pro', 'B2B Visibility'),
                ],
            ]
        );
    }

    // =====================================================
    // 5 — IMMOBILIER : Prestacia
    // =====================================================
    private function immobilierPrestaciaProject(): array
    {
        return $this->project(
            'prestacia.png',
            'Prestacia',
            'Prestacia',
            'Immobilier',
            'Plateforme et identité premium pour un promoteur immobilier de standing.',
            'Premium platform and identity for a high-end real-estate developer.',
            [
                'description_fr' => 'Prestacia propose des programmes immobiliers haut de gamme. Nous avons développé son univers digital : site de présentation des programmes avec galeries immersives, branding premium et stratégies d\'acquisition de leads qualifiés.',
                'description_en' => 'Prestacia offers premium real-estate programs. We built its digital universe: program showcase website with immersive galleries, premium branding and qualified lead-acquisition strategies.',
                'duration_fr' => 'Une collaboration de 8 mois avec Prestacia.',
                'duration_en' => 'An 8-month collaboration with Prestacia.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site vitrine des programmes avec galeries',
                        'Programs showcase website with galleries'
                    ),
                    $this->service(
                        'Branding', 'Branding',
                        'Identité premium du promoteur',
                        'Premium developer identity'
                    ),
                    $this->service(
                        'Marketing Digital', 'Digital Marketing',
                        'Campagnes d\'acquisition et landing pages',
                        'Lead-acquisition campaigns and landing pages'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('prestacia.png', 'DÉVELOPPEMENT WEB', 'WEB DEVELOPMENT', 'Programmes', 'Programs Showcase'),
                    $this->carouselCard('prestacia.png', 'BRANDING', 'BRANDING', 'Identity Premium', 'Premium Identity'),
                ],
            ]
        );
    }

    // =====================================================
    // 6 — IMMOBILIER : Maxsam
    // =====================================================
    private function immobilierMaxsamProject(): array
    {
        return $this->project(
            'maksamcovercard.jpg',
            'Maxsam',
            'Maxsam',
            'Immobilier',
            'Site commercial et communication digitale pour un opérateur immobilier.',
            'Sales website and digital communication for a real-estate operator.',
            [
                'description_fr' => 'Maxsam accompagne ses clients dans des projets immobiliers ambitieux. Notre intervention : un site performant mettant en valeur chaque projet, des outils de contact et de qualification, ainsi qu\'une ligne graphique cohérente sur tous les supports.',
                'description_en' => 'Maxsam supports its clients in ambitious real-estate projects. Our work: a high-performing website showcasing each project, contact and lead-qualification tools, and a consistent visual line across all media.',
                'duration_fr' => 'Une collaboration de 6 mois avec Maxsam.',
                'duration_en' => 'A 6-month collaboration with Maxsam.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site commercial avec présentation des projets',
                        'Sales website with project showcase'
                    ),
                    $this->service(
                        'Design UI / UX', 'UI / UX Design',
                        'Exploitation des maquettes et fiches projets',
                        'Mockup implementation and project sheets'
                    ),
                    $this->service(
                        'Community management', 'Community management',
                        'Animation des réseaux et publicité ciblée',
                        'Social media management and targeted ads'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('maksamcovercard.jpg', 'DÉVELOPPEMENT WEB', 'WEB DEVELOPMENT', 'Site Commercial', 'Sales Website'),
                    $this->carouselCard('maksamcovercard.jpg', 'ADS', 'ADS', 'Campagnes Ciblées', 'Targeted Campaigns'),
                ],
            ]
        );
    }

    // =====================================================
    // 7 — TOURISME : Oasis Beach
    // =====================================================
    private function tourismeOasisBeachProject(): array
    {
        return $this->project(
            'oasisbeach.jpg',
            'Oasis Beach',
            'Oasis Beach',
            'Tourisme',
            'Destination marketing digital pour un resort balnéaire : visuels, réservation et réseaux sociaux.',
            'A digital destination for a seaside resort: visuals, booking and social media.',
            [
                'description_fr' => 'Oasis Beach est une destination d\'évasion face à l\'océan. Nous avons construit son attractivité en ligne : site avec module de réservation, galerie immersive des espaces et campagnes social media pour remplir ses capacités en haute comme en basse saison.',
                'description_en' => 'Oasis Beach is an escape destination facing the ocean. We built its online appeal: website with booking module, immersive gallery of the spaces, and social media campaigns to fill capacity in both high and low season.',
                'duration_fr' => 'Une collaboration de 5 mois avec Oasis Beach.',
                'duration_en' => 'A 5-month collaboration with Oasis Beach.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site avec module de réservation en ligne',
                        'Website with online booking module'
                    ),
                    $this->service(
                        'Production audiovisuelle', 'Video production',
                        'Photos et vidéos immersives des espaces',
                        'Immersive photos and videos of the spaces'
                    ),
                    $this->service(
                        'Marketing Digital', 'Digital Marketing',
                        'Campagnes social media et influence',
                        'Social media and influencer campaigns'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('oasisbeach.jpg', 'RÉSERVATION', 'BOOKING', 'En Ligne', 'Online Booking'),
                    $this->carouselCard('oasisbeach.jpg', 'CONTENU VISUEL', 'VISUAL CONTENT', 'Photos & Vidéos', 'Photos & Videos'),
                ],
                'resultsContent' => [
                    'title_fr' => 'Résultats Social Media',
                    'title_en' => 'Social Media Results',
                    'subtitle_fr' => 'Une visibilité accrue qui remplit le resort, saison après saison.',
                    'subtitle_en' => 'Increased visibility that fills the resort, season after season.',
                    'tags_fr' => 'Instagram, Facebook, Tiktok',
                    'tags_en' => 'Instagram, Facebook, Tiktok',
                    'profileImage' => null,
                    'profileName_fr' => 'Oasis Beach',
                    'profileName_en' => 'Oasis Beach',
                    'profileRole_fr' => 'Resort balnéaire',
                    'profileRole_en' => 'Seaside resort',
                    'stats' => [
                        [
                            'platform' => 'instagram',
                            'views' => '1.8',
                            'viewsSuffix_fr' => 'M+ Vues',
                            'viewsSuffix_en' => 'M+ Views',
                            'subs' => '28',
                            'subsSuffix_fr' => 'K+ Abonnés',
                            'subsSuffix_en' => 'K+ Followers',
                        ],
                    ],
                    'carouselImages' => [],
                ],
            ]
        );
    }

    // =====================================================
    // 8 — HÔTELLERIE : Palais d'Orient
    // =====================================================
    private function hotelleriePalaisDorientProject(): array
    {
        return $this->project(
            'palaisdorient.png',
            'Palais d\'Orient',
            'Palais d\'Orient',
            'Hôtellerie',
            'Univers digital élégant pour un établissement d\'hôtellerie et de restauration.',
            'An elegant digital universe for a hospitality and dining venue.',
            [
                'description_fr' => 'Le Palais d\'Orient marie l\'hospitalité traditionnelle et le confort moderne. Nous avons traduit cet art de recevoir en digital : site vitrine raffiné, mise en valeur des espaces et des menus, et une stratégie de contenu qui attire une clientèle fidèle.',
                'description_en' => 'Palais d\'Orient blends traditional hospitality with modern comfort. We translated this art of hosting into digital: a refined showcase website, highlighted spaces and menus, and a content strategy that builds a loyal clientele.',
                'duration_fr' => 'Une collaboration de 6 mois avec le Palais d\'Orient.',
                'duration_en' => 'A 6-month collaboration with Palais d\'Orient.',
                'servicesOffered' => [
                    $this->service(
                        'Développement web', 'Web development',
                        'Site vitrine avec galeries des espaces',
                        'Showcase website with space galleries'
                    ),
                    $this->service(
                        'Branding', 'Branding',
                        'Identité élégante et supports imprimés',
                        'Elegant identity and print collateral'
                    ),
                    $this->service(
                        'Community management', 'Community management',
                        'Mise en valeur de la table et des événements',
                        'Showcasing the dining experience and events'
                    ),
                ],
                'offeredServicesCarousel' => [
                    $this->carouselCard('palaisdorient.png', 'DÉVELOPPEMENT WEB', 'WEB DEVELOPMENT', 'Site Vitrine', 'Showcase Website'),
                    $this->carouselCard('palaisdorient.png', 'BRANDING', 'BRANDING', 'Art de Recevoir', 'Art of Hosting'),
                ],
            ]
        );
    }
}
