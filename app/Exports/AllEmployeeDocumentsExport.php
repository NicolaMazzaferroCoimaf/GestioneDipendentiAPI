<?php

namespace App\Exports;

use App\Models\EmployeeDocument;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllEmployeeDocumentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return EmployeeDocument::with('employee', 'document')->get()->map(function ($doc) {
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
