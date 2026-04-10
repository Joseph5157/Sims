<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
        // Intentionally left blank.
    }
}

