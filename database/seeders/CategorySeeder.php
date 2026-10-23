<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Idempotent: creates categories by slug, updates existing ones.
     */
    public function run()
    {
        $categories = [
            // Service categories (existing)
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
            // Sector categories (used by the project cover cards)
            [
                'name_fr' => 'Immobilier',
                'name_en' => 'Real Estate',
                'slug' => 'immobilier',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Agroalimentaire',
                'name_en' => 'Agri-Food',
                'slug' => 'agroalimentaire',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Médical',
                'name_en' => 'Medical',
                'slug' => 'medical',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Hôtellerie',
                'name_en' => 'Hospitality',
                'slug' => 'hotellerie',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Tourisme',
                'name_en' => 'Tourism',
                'slug' => 'tourisme',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'name_fr' => 'Industrie meunière',
                'name_en' => 'Flour Milling Industry',
                'slug' => 'industrie-meuniere',
                'order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
