<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Employee::factory(15)->create()
            ->each(function ($emp) {
                // assegna 1–3 gruppi random
                $groups = \App\Models\Group::inRandomOrder()->take(rand(1,3))->pluck('id');
                $emp->groups()->attach($groups);

                // per ogni gruppo assegna documenti richiesti
                $docIds = \DB::table('group_document')
                             ->whereIn('group_id', $groups)
                             ->pluck('document_id')
                             ->unique();

                foreach ($docIds as $docId) {
                    $emp->documents()->attach($docId, [
                        'expiration_date' => now()->addMonths(rand(0,24))
                    ]);
                }
            });
    }
}
