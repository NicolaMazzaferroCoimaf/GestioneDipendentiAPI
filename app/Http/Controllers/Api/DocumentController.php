<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return Document::with('groups', 'employees')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:documents,name'
        ]);

        return Document::create($validated);
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

        $document->update($validated);
        return $document;
    }

    public function destroy(Document $document)
    {
        $document->delete();

        return response()->json(['message' => 'Documento eliminato con successo']);
    }
}
