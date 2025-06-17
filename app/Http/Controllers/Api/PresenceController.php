<?php

namespace App\Http\Controllers\Api;

use App\Models\Presence;
use Illuminate\Http\Request;
use App\Traits\LogsUserAction;
use App\Http\Controllers\Controller;

class PresenceController extends Controller
{

    use LogsUserAction;

    public function index()
    {
        return Presence::with('employee')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:ferie,malattia,assenza',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'note' => 'nullable|string'
        ]);

        $presence = Presence::create($validated);
        $this->logUserAction('audit', 'Created presence', $presence->toArray());

        return response()->json($presence, 201);
    }

    public function show(Presence $presence)
    {
        return $presence->load('employee');
    }

    public function update(Request $request, Presence $presence)
    {
        $validated = $request->validate([
            'type' => 'in:ferie,malattia,assenza',
            'start_date' => 'date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'note' => 'nullable|string'
        ]);

        $presence->update($validated);
        $this->logAction('Updated', 'Presence', $presence->toArray());

        return response()->json($presence);
    }

    public function destroy(Presence $presence)
    {
        $presence->delete();
        $this->logAction('Deleted', 'Presence', $presence->toArray());

        return response()->json(null, 204);
    }
}
