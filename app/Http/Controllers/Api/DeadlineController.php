<?php

namespace App\Http\Controllers\Api;

use App\Models\Deadline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeadlineController extends Controller
{
    public function index() 
    { 
        return Deadline::with('tags')->get(); 
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'expiration_date' => 'required|date',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'file' => 'nullable|file|max:5120',
        ]);

        if ($r->hasFile('file')) {
            $data['file_path'] = $r->file('file')->store('deadlines','public');
        }

        $deadline = Deadline::create($data);
        if (!empty($data['tags'])) $deadline->tags()->sync($data['tags']);

        return response()->json($deadline->load('tags'),201);
    }

    public function show(Deadline $deadline) { return $deadline->load('tags'); }

    public function update(Request $r, Deadline $deadline)
    {
        $data = $r->validate([
            'title' => 'string',
            'description' => 'nullable|string',
            'expiration_date' => 'date',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'file' => 'nullable|file|max:5120',
        ]);

        if ($r->hasFile('file')) {
            if ($deadline->file_path) Storage::disk('public')->delete($deadline->file_path);
            $data['file_path'] = $r->file('file')->store('deadlines','public');
        }

        $deadline->update($data);
        if (isset($data['tags'])) $deadline->tags()->sync($data['tags']);

        return $deadline->load('tags');
    }

    public function destroy(Deadline $deadline)
    {
        if ($deadline->file_path) Storage::disk('public')->delete($deadline->file_path);
        $deadline->delete();
        return response()->noContent();
    }

    public function expired() 
    { 
        return Deadline::expired()->with('tags')->get(); 
    }

    public function expiring(Request $r)
    {
        $days = $r->query('days',30);
        return Deadline::expiring($days)->with('tags')->get();
    }
}
