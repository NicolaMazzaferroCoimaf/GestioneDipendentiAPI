<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Traits\LogsUserAction;

class DocumentController extends Controller
{
    use LogsUserAction;

    public function index()
    {
        return Document::with('groups', 'employees')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:documents,name'
        ]);

        $document = Document::create($validated);

        $this->logUserAction('audit', 'Documento creato', [
            'document_id' => $document->id,
            'name' => $document->name
        ]);

        return $document;
    }

    public function show(Document $document)
    {
        return $document->load('groups', 'employees');
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:documents,name,' . $document->id
        ]);

        $oldName = $document->name;
        $document->update($validated);

        $this->logUserAction('audit', 'Documento aggiornato', [
            'document_id' => $document->id,
            'old_name' => $oldName,
            'new_name' => $validated['name']
        ]);

        return $document;
    }

    public function destroy(Document $document)
    {
        $id = $document->id;
        $name = $document->name;
        $document->delete();

        $this->logUserAction('audit', 'Documento eliminato', [
            'document_id' => $id,
            'name' => $name
        ]);

        return response()->json(['message' => 'Documento eliminato con successo']);
    }
}
