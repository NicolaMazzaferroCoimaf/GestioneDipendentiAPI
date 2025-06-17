<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        // Gruppi principali
        $officina   = \App\Models\Group::firstOrCreate(['name' => 'Officina']);
        $magazzino  = \App\Models\Group::firstOrCreate(['name' => 'Magazzino']);
        $dipendenti = \App\Models\Group::firstOrCreate(['name' => 'GESTIONALE-Dipendenti']);

        // Associa qualche documento a ciascun gruppo
        $docs = \App\Models\Document::inRandomOrder()->take(4)->pluck('id');
        $officina->documents()->sync($docs);

        $magazzino->documents()->sync(
            \App\Models\Document::inRandomOrder()->take(3)->pluck('id')
        );

        $dipendenti->documents()->sync(
            \App\Models\Document::where('name','Documento Identità')->pluck('id')
        );
    }
}
