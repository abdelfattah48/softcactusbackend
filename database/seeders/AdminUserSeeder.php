<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the initial admin user for the backoffice.
     *
     * Idempotent: if the admin email already exists, it is left untouched
     * (so a password changed from the backoffice is never overwritten).
     *
     * Credentials can be overridden via environment variables:
     *   ADMIN_EMAIL, ADMIN_PASSWORD
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        if (User::where('email', $email)->exists()) {
            $this->command->info("Admin user {$email} already exists — skipping.");
            return;
        }

        // NOTE: the login endpoint validates passwords with min:8,
        // so the default password must be at least 8 characters.
        $password = env('ADMIN_PASSWORD', 'Admin@12345');

        User::create([
            'name' => 'Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
            'permissions' => [
                'dashboard.read',
                'dashboard.write',
                'users.read',
                'users.create',
                'users.update',
                'users.delete',
                'projects.read',
                'projects.create',
                'projects.update',
                'projects.delete',
                'why-us.read',
                'why-us.update',
            ],
        ]);

        $this->command->info("Admin user created: {$email} / {$password}");
    }
}
