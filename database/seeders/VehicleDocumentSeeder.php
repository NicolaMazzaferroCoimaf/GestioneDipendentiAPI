<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleDocument;

class VehicleDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $docs = ['Revisione', 'Bollo', 'Assicurazione', 'Tagliando'];

        Vehicle::all()->each(function (Vehicle $v) use ($docs) {
            foreach ($docs as $doc) {
                VehicleDocument::create([
                    'vehicle_id' => $v->id,
                    'name' => $doc,
                    'file_path' => 'vehicle_docs/sample.pdf',
                    'execution_date' => now()->subMonths(rand(1,10)),
                    'expiration_date' => now()->addMonths(rand(6,18)),
                ]);
            }
        });
    }
}
