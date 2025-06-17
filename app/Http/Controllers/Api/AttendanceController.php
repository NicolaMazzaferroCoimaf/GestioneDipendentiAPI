<?php

namespace App\Http\Controllers\Api;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function index()
    {
        return Attendance::with('employee')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'note' => 'nullable|string',
        ]);

        return Attendance::create($data);
    }

    public function show(Attendance $attendance)
    {
        return $attendance->load('employee');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'note' => 'nullable|string',
        ]);

        $attendance->update($data);

        return $attendance;
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Presenza eliminata con successo']);
    }
}
