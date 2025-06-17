<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deadline;
use App\Models\Tag;

class DeadlineSeeder extends Seeder
{
    public function run(): void
    {
        Deadline::factory(20)->create()->each(function (Deadline $dl) {
            $tagIds = Tag::inRandomOrder()->take(rand(1,3))->pluck('id');
            $dl->tags()->attach($tagIds);
        });

        $iso = Deadline::firstOrCreate([
            'title' => 'Audit ISO 9001',
            'description' => 'Verifica annuale certificazione qualità',
            'file_path' => 'deadlines/sample.pdf',
            'expiration_date' => now()->addMonths(6),
        ]);
        $iso->tags()->sync(Tag::whereIn('name', ['ISO9001', 'Qualità'])->pluck('id'));

        $dpi = Deadline::firstOrCreate([
            'title' => 'Scadenza DPI Mario Rossi',
            'description' => 'Verifica sostituzione calzature antinfortunistiche',
            'file_path' => 'deadlines/sample.pdf',
            'expiration_date' => now()->addDays(90),
        ]);
        $dpi->tags()->sync(Tag::where('name', 'Sicurezza')->pluck('id'));
    }
}
