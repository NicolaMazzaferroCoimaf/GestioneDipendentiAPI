<?php

namespace App\Http\Controllers\Api;

use App\Models\Group;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Traits\LogsUserAction;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EmployeeDocumentImage;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    use LogsUserAction;

    /* ---------------------- *
     * operazioni sui GRUPPI  *
     * ---------------------- */

    public function assignGroup(Request $request, Employee $employee)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id'
        ]);

        $groupId = $request->input('group_id');

        DB::beginTransaction();

        try {
            // Assegna il gruppo al dipendente (evita duplicati automaticamente)
            $employee->groups()->syncWithoutDetaching([$groupId]);

            // Recupera i documenti associati al gruppo
            $group = Group::with('documents')->findOrFail($groupId);

            $newDocs = 0;

            foreach ($group->documents as $document) {
                // Controlla se il dipendente ha già questo documento
                $alreadyAssigned = EmployeeDocument::where('employee_id', $employee->id)
                    ->where('document_id', $document->id)
                    ->exists();

                if (!$alreadyAssigned) {
                    // Crea un nuovo documento per il dipendente (scadenza null di default)
                    EmployeeDocument::create([
                        'employee_id' => $employee->id,
                        'document_id' => $document->id,
                        'expiration_date' => null,
                    ]);
                    $newDocs++;
                }
            }

            DB::commit();

            $this->logUserAction('audit', 'Assegnato gruppo a dipendente', [
                'employee_id' => $employee->id,
                'group_id' => $groupId,
                'documenti_assegnati' => $newDocs
            ]);

            return response()->json([
                'message' => 'Gruppo assegnato con successo e documenti aggiornati.',
                'employee_id' => $employee->id,
                'group_id' => $group->id,
                'document_count' => $group->documents->count()
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errore durante l\'assegnazione.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignMultipleGroups(Request $request, Employee $employee)
    {
        $request->validate([
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id'
        ]);

        $groupIds = $request->input('group_ids');

        DB::beginTransaction();

        try {
            // Assegna tutti i gruppi al dipendente
            $employee->groups()->syncWithoutDetaching($groupIds);

            // Recupera tutti i documenti da tutti i gruppi
            $documentIds = DB::table('group_document')
                ->whereIn('group_id', $groupIds)
                ->pluck('document_id')
                ->unique()
                ->toArray();

            // Recupera documenti già assegnati
            $alreadyAssignedDocs = EmployeeDocument::where('employee_id', $employee->id)
                ->pluck('document_id')
                ->toArray();

            // Calcola documenti mancanti
            $newDocs = array_diff($documentIds, $alreadyAssignedDocs);

            // Crea nuovi documenti
            foreach ($newDocs as $docId) {
                EmployeeDocument::create([
                    'employee_id' => $employee->id,
                    'document_id' => $docId,
                    'expiration_date' => null,
                ]);
            }

            DB::commit();

            $this->logUserAction('audit', 'Assegnati gruppi multipli al dipendente', [
                'employee_id' => $employee->id,
                'group_ids' => $groupIds,
                'nuovi_documenti' => count($newDocs)
            ]);

            return response()->json([
                'message' => 'Gruppi e documenti assegnati con successo.',
                'employee_id' => $employee->id,
                'group_count' => count($groupIds),
                'new_documents_added' => count($newDocs),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errore durante l\'assegnazione multipla.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function detachGroup(Request $request, Employee $employee)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id'
        ]);

        $groupId = $request->input('group_id');

        DB::beginTransaction();

        try {
            // Rimuovi il gruppo dal dipendente
            $employee->groups()->detach($groupId);

            // Recupera i documenti collegati a quel gruppo
            $groupDocumentIds = DB::table('group_document')
                ->where('group_id', $groupId)
                ->pluck('document_id')
                ->toArray();

            // Recupera tutti gli altri gruppi ancora assegnati
            $otherGroupIds = $employee->groups()->pluck('groups.id')->toArray();

            // Recupera tutti i documenti ancora richiesti dagli altri gruppi
            $otherRequiredDocs = DB::table('group_document')
                ->whereIn('group_id', $otherGroupIds)
                ->pluck('document_id')
                ->unique()
                ->toArray();

            // Calcola i documenti da rimuovere (presenti solo nel gruppo rimosso)
            $docsToRemove = array_diff($groupDocumentIds, $otherRequiredDocs);

            // Elimina i documenti orfani da employee_documents
            EmployeeDocument::where('employee_id', $employee->id)
                ->whereIn('document_id', $docsToRemove)
                ->delete();

            DB::commit();

            $this->logUserAction('audit', 'Gruppo rimosso da dipendente', [
                'employee_id' => $employee->id,
                'group_id' => $groupId,
                'documenti_rimossi' => count($docsToRemove)
            ]);

            return response()->json([
                'message' => 'Gruppo rimosso e documenti aggiornati.',
                'group_id' => $groupId,
                'documents_removed' => count($docsToRemove),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errore durante la rimozione del gruppo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* ---------------------- *
     * operazioni sui DOC     *
     * ---------------------- */

    public function assignDocument(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'document_id'     => 'required|exists:documents,id',
            // opzionale – se vuoi salvare già la scadenza
            'expiration_date' => 'nullable|date|after_or_equal:today',
        ]);

        // evita duplicati sul pivot employee_documents
        $already = EmployeeDocument::where('employee_id', $employee->id)
            ->where('document_id', $data['document_id'])
            ->exists();

        if ($already) {
            return response()->json([
                'message' => 'Documento già assegnato a questo dipendente'
            ], 409);
        }

        $pivot = EmployeeDocument::create([
            'employee_id'     => $employee->id,
            'document_id'     => $data['document_id'],
            'expiration_date' => $data['expiration_date'] ?? null,
        ]);

        // ► log audit
        $this->logUserAction('audit', 'Assegnato documento a dipendente', [
            'employee_id' => $employee->id,
            'document_id' => $data['document_id'],
        ]);

        return response()->json([
            'message' => 'Documento assegnato con successo',
            'pivot'   => $pivot
        ], 201);
    }

    public function updateExpiration(Request $request, Employee $employee, Document $document)
    {
        $data = $request->validate([
            'expiration_date' => 'required|date|after_or_equal:today',
        ]);

        // 🔹 se esiste aggiorna, altrimenti crea la riga pivot
        $pivot = EmployeeDocument::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'document_id' => $document->id,
            ],
            [
                'expiration_date' => $data['expiration_date'],
            ]
        );

        $this->logUserAction('audit', 'Aggiornata scadenza documento', [
            'employee_id'     => $employee->id,
            'document_id'     => $document->id,
            'expiration_date' => $data['expiration_date'],
        ]);

        return response()->json([
            'message'         => 'Scadenza salvata con successo',
            'pivot'           => $pivot,
        ], 200);
    }

    public function uploadDocumentImages(Request $request, $employeeDocumentId)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $document = EmployeeDocument::findOrFail($employeeDocumentId);
        $paths = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('employee_documents', 'public');
            $document->images()->create(['path' => $path]);
            $paths[] = $path;
        }

        $this->logUserAction('audit', 'Immagini documento caricate', [
            'employee_document_id' => $employeeDocumentId,
            'paths' => $paths
        ]);

        return response()->json([
            'message' => 'Immagini caricate con successo',
            'files' => $paths
        ]);
    }

    public function uploadAttachments(Request $request, $employeeDocumentId)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,doc,docx|max:5120'
        ]);

        $doc = EmployeeDocument::findOrFail($employeeDocumentId);
        $paths = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('employee_documents/files', 'public');
            $doc->images()->create(['path' => $path]);
            $paths[] = $path;
        }

        $this->logUserAction('audit', 'Allegati caricati', [
            'employee_document_id' => $employeeDocumentId,
            'files' => $paths
        ]);

        return response()->json(['message' => 'Allegati caricati con successo']);
    }

    public function getImages($employeeDocumentId)
    {
        $document = EmployeeDocument::with('images')->findOrFail($employeeDocumentId);

        $this->logUserAction('audit', 'Visualizzazione immagini documento', [
            'employee_document_id' => $employeeDocumentId
        ]);

        return response()->json([
            'employee_document_id' => $document->id,
            'images' => $document->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => asset('storage/' . $img->path),
                    'path' => $img->path
                ];
            }),
        ]);
    }

    public function deleteImage($imageId)
    {
        $image = EmployeeDocumentImage::findOrFail($imageId);

        // Elimina il file fisico
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        $this->logUserAction('uploads', 'Immagine eliminata', [
            'image_id' => $imageId
        ]);

        return response()->json([
            'message' => 'Immagine eliminata con successo'
        ]);
    }
}