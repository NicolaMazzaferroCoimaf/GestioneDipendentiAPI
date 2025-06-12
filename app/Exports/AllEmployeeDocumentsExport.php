<?php

namespace App\Exports;

use App\Models\EmployeeDocument;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllEmployeeDocumentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $documents = EmployeeDocument::with('employee', 'document')->get();

        $user = auth()->check() ? auth()->user()->email : 'console/system';

        Log::channel('exports')->info("[AllEmployeeDocumentsExport] Utente: {$user} ha esportato {$documents->count()} documenti.");

        return $documents->map(function ($doc) {
            return [
                'Dipendente' => $doc->employee->name,
                'Documento' => $doc->document->name,
                'Scadenza' => $doc->expiration_date,
            ];
        });
    }

    public function headings(): array
    {
        return ['Dipendente', 'Documento', 'Data Scadenza'];
    }
}
