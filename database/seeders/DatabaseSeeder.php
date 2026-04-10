<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Central database seeder.
     *
     * Tenants/domains are managed by the central app, but tenant demo data
     * (users, roles, institution records) must be seeded via `tenants:seed`.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_super_admin' => true,
            ],
        );
    }
}
