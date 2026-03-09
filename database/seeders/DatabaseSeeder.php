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
            MenusAndItemsSqlSeeder::class,
            InventorySeeder::class,
            RecipeSeeder::class,
            UserSeeder::class,
            VerifySuperAdminSeeder::class,
        ]);
    }
}
