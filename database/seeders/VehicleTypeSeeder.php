<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        collect(['Furgone','Autovettura','Camion','Carrello'])->each(fn($n) =>
            VehicleType::firstOrCreate(['name'=>$n])
        );
    }
}
