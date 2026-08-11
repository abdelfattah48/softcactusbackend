<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create initial admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'permissions' => ['dashboard.read', 'dashboard.write', 'users.read', 'users.create', 'users.update', 'users.delete', 'projects.read', 'projects.create', 'projects.update', 'projects.delete', 'why-us.read', 'why-us.update'],
        ]);
    }
}
