<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleType;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        // Assicurati di avere tipi (VehicleTypeSeeder) prima
        if (VehicleType::count() === 0) {
            $this->call(VehicleTypeSeeder::class);
        }

        // crea 25 veicoli random
        Vehicle::factory()->count(30)->create();
    }
}