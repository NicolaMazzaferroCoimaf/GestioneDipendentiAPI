<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EmployeeDocument;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeDocumentsExport;
use App\Exports\AllEmployeeDocumentsExport;

class EmployeeController extends Controller
{

    public function index()
    {
        return Employee::with('groups', 'employeeDocuments.document')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        return Employee::create($validated);
    }

    public function show(Employee $employee)
    {
        return $employee->load('groups', 'employeeDocuments.document');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $employee->update($validated);
        return $employee;
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['message' => 'Dipendente eliminato']);
    }

    public function getDocuments(Employee $employee)
    {
        $documents = $employee->employeeDocuments()
            ->with('document')
            ->get()
            ->map(function ($doc) {
                return [
                    'document_id' => $doc->document->id,
                    'document_name' => $doc->document->name,
                    'expiration_date' => $doc->expiration_date,
                ];
            });

        return response()->json($documents);
    }

    public function expiring(Request $request)
    {
        $days = $request->query('days', 30);
        $threshold = Carbon::now()->addDays($days)->startOfDay();

        $docs = EmployeeDocument::with('document','employee')
            ->whereDate('expiration_date', '<=', $threshold)
            ->whereDate('expiration_date', '>=', Carbon::now())
            ->get();

        return response()->json($docs);
    }

    public function expired()
    {
        $docs = EmployeeDocument::with('document','employee')
            ->whereDate('expiration_date', '<', Carbon::now())
            ->get();

        return response()->json($docs);
    }

    public function missingDocuments(Employee $employee)
    {
        $required = $employee->groups()->with('documents')->get()
                        ->pluck('documents')
                        ->flatten()->pluck('id')->unique()->toArray();

        $assigned = $employee->employeeDocuments()->pluck('document_id')->toArray();

        $missing = array_diff($required, $assigned);

        $docs = Document::whereIn('id', $missing)->get();

        return response()->json(['missing_documents' => $docs]);
    }

    public function exportDocuments($employeeId)
    {
        return Excel::download(
            new EmployeeDocumentsExport($employeeId),
            "employee-{$employeeId}-documents.xlsx"
        );
    }

    public function exportDocumentsPdf($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $documents = $employee->employeeDocuments()->with('document')->get();

        $pdf = Pdf::loadView('exports.employee_documents', compact('employee', 'documents'));

        return $pdf->download("employee-{$employee->id}-documents.pdf");
    }

    public function exportAllDocumentsExcel()
    {
        return Excel::download(
            new AllEmployeeDocumentsExport(),
            'tutti-i-documenti.xlsx'
        );
    }
}