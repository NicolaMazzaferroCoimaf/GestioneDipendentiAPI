<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Patente', 'Carta d’identità', 'Permesso di soggiorno',
                'Corso Sicurezza', 'Visita Medica', 'Certificato Antincendio',
                'HACCP', 'Certificato Carrellista', 'Attestato Primo Soccorso'
            ]),
            'created_at' => now(),
        ];
    }
}
