<?php

namespace App\Exports;

use App\Models\EmployeeDocument;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeDocumentsExport implements FromCollection, WithHeadings
{
    protected $employeeId;

    public function __construct($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function collection()
    {
        return EmployeeDocument::with('document')
            ->where('employee_id', $this->employeeId)
            ->get()
            ->map(function ($doc) {
                return [
                    'Documento' => $doc->document->name,
                    'Scadenza' => $doc->expiration_date,
                ];
            });
    }

    public function headings(): array
    {
        return ['Documento', 'Data Scadenza'];
    }
}
