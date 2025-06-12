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
        $docs = EmployeeDocument::with('document')
            ->where('employee_id', $this->employeeId)
            ->get();

        $count = $docs->count();
        $user = auth()->check() ? auth()->user()->email : 'guest';

        Log::channel('exports')->info("[EmployeeDocumentsExport] Utente {$user} ha esportato {$count} documenti per dipendente ID {$this->employeeId}.");

        return $docs->map(function ($doc) {
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
