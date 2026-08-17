<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $videoUrl = 'https://cdn.jsdelivr.net/gh/abdelfattah48/video-cdn-6@main/Video header-1280x720.mp4';
        $coverImages = [
            'aQ1ph8tQn6UlYZYjR70zAGaQ7syBgBBZjge8hufY.png',
            'EhG6oiTTxswZoDqpMnOcPUJ0a7m2yLIXmdrwrb2G.png',
            'GO6MA9wVWdInnPISbuEXrpmDPP4xNrbuCaR76oRS.png',
            'J5VaniGHV0V0WOrzSXTjDMRQr76I6yHxFc0Y0ZvV.png',
            'JHJQrcWzVlchrPtXo511OUYUWWQXPdVr7xJTAgdO.png',
            'jvB8giZohOqQY0mqIC1bVKHkGO84LLGt9Urospiq.png',
            'lh12AZCEH8xsTFjELNWIgbiF4GD0vZKSkBA5faOB.png',
            'LtjhnERIQ1CQZwGCgXGgHPBxg0FmNEiBzMqYO4ux.png',
            'MbGdCpUhFKRqdcLfOhDLcpCloFstOfAVTOy4CgwO.png',
            'pmJtJtnD1YlF0yE4I0WpviJwqyFpsccat8WIBMAc.png',
            'RxK1Ec8OLZqHuncOpR5CXbrjvRyjxgqrgf5weUD5.png',
        ];

        $projects = [
            [
                'status' => 'published',
                'public_card' => [
                    'name' => 'TechStart App',
                    'category' => 'Stratégie & Branding',
                    'cover' => '/storage/projects/' . $coverImages[0],
                    'videos' => [
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                    ],
                ],
                'details' => [
                    'client' => 'TechStart SARL',
                    'year' => '2025',
                    'services' => ['Stratégie digitale', 'Design UI/UX', 'Développement mobile'],
                    'description' => 'Application mobile innovante pour une startup technologique, comprenant une interface utilisateur moderne et des fonctionnalités avancées.',
                ],
            ],
            [
                'status' => 'published',
                'public_card' => [
                    'name' => 'Luxury Brand Website',
                    'category' => 'Développement web',
                    'cover' => '/storage/projects/' . $coverImages[1],
                    'videos' => [
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                    ],
                ],
                'details' => [
                    'client' => 'Maison de Luxe',
                    'year' => '2025',
                    'services' => ['Site web e-commerce', 'SEO', 'Maintenance'],
                    'description' => 'Site e-commerce haut de gamme avec expérience utilisateur premium et intégration de paiement sécurisé.',
                ],
            ],
            [
                'status' => 'published',
                'public_card' => [
                    'name' => 'HealthCare Platform',
                    'category' => 'Marketing Digital',
                    'cover' => '/storage/projects/' . $coverImages[2],
                    'videos' => [
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                    ],
                ],
                'details' => [
                    'client' => 'HealthCare Plus',
                    'year' => '2024',
                    'services' => ['Marketing digital', 'SEO', 'Gestion des réseaux sociaux'],
                    'description' => 'Campagne digitale complète pour une plateforme de santé, augmentant la visibilité de 200%.',
                ],
            ],
            [
                'status' => 'published',
                'public_card' => [
                    'name' => 'Corporate Video Production',
                    'category' => 'Production Audiovisuelle',
                    'cover' => '/storage/projects/' . $coverImages[3],
                    'videos' => [
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                    ],
                ],
                'details' => [
                    'client' => 'Global Corp',
                    'year' => '2024',
                    'services' => ['Production vidéo', 'Montage', 'Motion design'],
                    'description' => 'Vidéo corporative professionnelle présentant les valeurs et l\'histoire de l\'entreprise.',
                ],
            ],
            [
                'status' => 'published',
                'public_card' => [
                    'name' => 'E-commerce Redesign',
                    'category' => 'Développement web',
                    'cover' => '/storage/projects/' . $coverImages[4],
                    'videos' => [
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                    ],
                ],
                'details' => [
                    'client' => 'ShopNow',
                    'year' => '2024',
                    'services' => ['Refonte site web', 'UX Design', 'Migration technique'],
                    'description' => 'Refonte complète d\'une plateforme e-commerce avec amélioration du taux de conversion de 35%.',
                ],
            ],
            [
                'status' => 'published',
                'public_card' => [
                    'name' => 'Brand Identity',
                    'category' => 'Stratégie & Branding',
                    'cover' => '/storage/projects/' . $coverImages[5],
                    'videos' => [
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                        ['url' => $videoUrl],
                    ],
                ],
                'details' => [
                    'client' => 'Nouveau Départ',
                    'year' => '2024',
                    'services' => ['Identité visuelle', 'Charte graphique', 'Supports de communication'],
                    'description' => 'Création d\'une identité visuelle complète pour une nouvelle entreprise ambitieuse.',
                ],
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}