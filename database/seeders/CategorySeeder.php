<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name_fr' => 'Stratégie & Branding',
                'name_en' => 'Strategy & Branding',
                'slug' => 'strategie-branding',
                'order' => 0,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Développement web',
                'name_en' => 'Web Development',
                'slug' => 'developpement-web',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Marketing Digital',
                'name_en' => 'Digital Marketing',
                'slug' => 'marketing-digital',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Production Audiovisuelle',
                'name_en' => 'Audiovisual Production',
                'slug' => 'production-audiovisuelle',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}