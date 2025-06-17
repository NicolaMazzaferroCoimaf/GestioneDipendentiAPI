<?php

namespace Database\Seeders;

use Database\Seeders\TagSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\VehicleSeeder;
use Database\Seeders\DeadlineSeeder;
use Database\Seeders\DocumentSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\VehicleTypeSeeder;
use Database\Seeders\VehicleDocumentSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ordine corretto per chiavi esterne
        $this->call([
            TagSeeder::class,
            DocumentSeeder::class,
            GroupSeeder::class,
            VehicleTypeSeeder::class,
            EmployeeSeeder::class,
            VehicleSeeder::class,
            UserSeeder::class,
            VehicleDocumentSeeder::class,
            DeadlineSeeder::class,
        ]);
    }
}