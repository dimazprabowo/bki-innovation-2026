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
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CompanySeeder::class,
            SystemConfigurationSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            CompetencySeeder::class,
            PersonelSeeder::class,
            PeralatanSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
