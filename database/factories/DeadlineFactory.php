<?php

namespace Database\Factories;

use App\Models\Deadline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DeadlineFactory extends Factory
{
    protected $model = Deadline::class;

    public function definition(): array
    {
        Storage::disk('public')->makeDirectory('deadlines');
        $fakePath = 'deadlines/'.Str::uuid().'.pdf';
        Storage::disk('public')->put($fakePath, $this->faker->paragraph()); // contenuto dummy

        return [
            'title' => $this->faker->sentence(3), // es. “Controllo estintori”
            'description' => $this->faker->sentence(8),
            'expiration_date' => $this->faker->dateTimeBetween('+10 days', '+18 months'),
            'file_path' => $fakePath, // path nel disco public
            'created_at' => now(),
        ];
    }
}
