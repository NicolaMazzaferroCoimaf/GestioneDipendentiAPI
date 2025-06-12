<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Traits\LogsUserAction;

class GroupController extends Controller
{
    use LogsUserAction;

    public function index()
    {
        return Group::with('documents')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|unique:groups,name']);

        $group = Group::create($validated);

        $this->logUserAction('audit', 'Gruppo creato', [
            'group_id' => $group->id,
            'name' => $group->name
        ]);

        return $group;
    }

    public function show(Group $group)
    {
        return $group->load('documents');
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate(['name' => 'required|string|unique:groups,name,' . $group->id]);

        $group->update($validated);

        $this->logUserAction('audit', 'Gruppo aggiornato', [
            'group_id' => $group->id,
            'name' => $group->name
        ]);

        return $group;
    }

    public function destroy(Group $group)
    {
        $id = $group->id;
        $name = $group->name;
        $group->delete();

        $this->logUserAction('audit', 'Gruppo eliminato', [
            'group_id' => $id,
            'name' => $name
        ]);

        return response()->json(['message' => 'Gruppo eliminato']);
    }

    public function detachDocuments(Request $request, Group $group)
    {
        $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
        ]);

        $group->documents()->detach($request->document_ids);

        $this->logUserAction('audit', 'Documenti rimossi dal gruppo', [
            'group_id' => $group->id,
            'documents_detached' => $request->document_ids
        ]);

        return response()->json([
            'message' => 'Documenti rimossi dal gruppo con successo',
            'group_id' => $group->id,
            'documents_detached' => $request->document_ids
        ]);
    }
}
