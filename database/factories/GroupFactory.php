<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Officina', 'Montaggio', 'Magazzino', 'Amministrazione',
                'Commerciale', 'R&S', 'IT', 'Qualità', 'Logistica'
            ]),
        ];
    }
}
