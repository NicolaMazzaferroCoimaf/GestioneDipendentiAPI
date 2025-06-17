<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Sicurezza', 'ISO9001', 'Formazione', 'Qualità'];
        foreach ($names as $n) {
            \App\Models\Tag::firstOrCreate(['name'=>$n]);
        }
    }
}