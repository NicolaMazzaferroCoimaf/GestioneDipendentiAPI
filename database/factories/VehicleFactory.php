<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $brandModels = [
            'Fiat'     => ['Ducato', 'Fiorino'],
            'Ford'     => ['Transit', 'Custom'],
            'Renault'  => ['Master', 'Trafic'],
            'Mercedes' => ['Sprinter', 'Vito'],
        ];

        // scegli marca & modello coerenti
        $brand  = array_rand($brandModels);              // es. "Fiat"
        $model  = $this->faker->randomElement($brandModels[$brand]);

        return [
            'vehicle_type_id'   => VehicleType::inRandomOrder()->value('id'),
            'brand'             => $brand,
            'model'             => $model,
            'license_plate'     => strtoupper($this->faker->bothify('??###??')),   // es. AB123CD
            'vin'               => strtoupper($this->faker->bothify('#################')),
            'registration_year' => $this->faker->numberBetween(2005, now()->year),
        ];
    }
}
