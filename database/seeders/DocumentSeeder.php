<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Document::create(['name' => "Documento $i"]);
        }

        Document::firstOrCreate(['name' => 'Patente']);
        Document::firstOrCreate(['name' => 'Documento Identità']);
    }
}
