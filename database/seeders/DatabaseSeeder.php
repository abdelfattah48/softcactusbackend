<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create initial admin user (see AdminUserSeeder)
        $this->call(AdminUserSeeder::class);

        // Call other seeders
        $this->call([
            QuiSommesNousSeeder::class,
            NotreHistoireSeeder::class,
            ServicesSectionSeeder::class,
            CategorySeeder::class,   // categories first (sectors used by projects)
            ProjectSeeder::class,    // then the 8 portfolio projects
        ]);
    }
}
