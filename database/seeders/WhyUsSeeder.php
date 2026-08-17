<?php

namespace Database\Seeders;

use App\Models\WhyUsCard;
use Illuminate\Database\Seeder;

class WhyUsSeeder extends Seeder
{
    public function run(): void
    {
        $videoUrl = 'https://cdn.jsdelivr.net/gh/abdelfattah48/video-cdn-6@main/Video header-1280x720.mp4';
        $coverImages = [
            '0QddrtPBwzuRnB4FNHTUD2Eu8eLEvlr8XXtZsHzN.png',
            '5jML6ighWzJinT148dwVWNXFks1xwjqzSDqUpsJA.png',
            'F5HNThiW5nuHfdHaQ5Q3ckhVXUER9c4GuHonbGfU.png',
            'isj7FVOWXnrkvqpt8TVgFapYzQDNak5zgZdB1lfZ.png',
            'LoDvCaD5OaAwNFuBUR0OYeDbp54SF9biPtyV5DRM.png',
            'Lz5OpbpHzdlQDI5hA0CAhxbrKUC0IwEzomH6o6io.png',
            'rIsR2QWDktWB0YBSVpJ1wVM9NMs2wEmyUitSWdFj.png',
            'sm0omVgn4xhRVdGSe8HdvliyoTLLOmVMROblSrG5.png',
        ];

        $cards = [
            [
                'name' => 'Sophie Martin',
                'role' => 'DIRECTRICE CRÉATIVE',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[0],
                'video_url' => $videoUrl,
                'sort_order' => 0,
            ],
            [
                'name' => 'Thomas Dubois',
                'role' => 'DÉVELOPPEUR SENIOR',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[1],
                'video_url' => $videoUrl,
                'sort_order' => 1,
            ],
            [
                'name' => 'Emma Bernard',
                'role' => 'STRATÈGE DIGITAL',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[2],
                'video_url' => $videoUrl,
                'sort_order' => 2,
            ],
            [
                'name' => 'Lucas Petit',
                'role' => 'RÉALISATEUR VIDÉO',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[3],
                'video_url' => $videoUrl,
                'sort_order' => 3,
            ],
            [
                'name' => 'Chloé Moreau',
                'role' => 'CHARGÉE DE PROJET',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[4],
                'video_url' => $videoUrl,
                'sort_order' => 4,
            ],
            [
                'name' => 'Antoine Robert',
                'role' => 'DESIGNER UI/UX',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[5],
                'video_url' => $videoUrl,
                'sort_order' => 5,
            ],
            [
                'name' => 'Julie Durand',
                'role' => 'RESPONSABLE MARKETING',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[6],
                'video_url' => $videoUrl,
                'sort_order' => 6,
            ],
            [
                'name' => 'Maxime Leroy',
                'role' => 'DÉVELOPPEUR BACKEND',
                'cover_url' => '/storage/why-us/covers/' . $coverImages[7],
                'video_url' => $videoUrl,
                'sort_order' => 7,
            ],
        ];

        foreach ($cards as $card) {
            WhyUsCard::create($card);
        }
    }
}