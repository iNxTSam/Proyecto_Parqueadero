<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogosSeeder::class,
            TarifasSeeder::class,
            BahiasSeeder::class,
            UsuarioDemoSeeder::class,
            ClienteDemoSeeder::class,
        ]);
    }
}
