<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleDocumentFactory extends Factory
{
    protected $model = VehicleDocument::class;

    public function definition(): array
    {
        $names = ['Assicurazione RC', 'Bollo', 'Revisione', 'Tagliando', 'Cronotachigrafo'];

        $exec = $this->faker->dateTimeBetween('-12 months', 'now');
        $exp  = (clone $exec)->modify('+1 year');

        return [
            'vehicle_id' => Vehicle::inRandomOrder()->first()->id,
            'name' => $this->faker->randomElement($names),
            'file_path' => null,
            'execution_date' => $exec,
            'expiration_date' => $exp,
        ];
    }
}
