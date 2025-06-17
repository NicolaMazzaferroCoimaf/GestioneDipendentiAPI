<?php

namespace App\Http\Controllers\Api;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\VehicleDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class VehicleDocumentController extends Controller
{
    use LogsUserAction;

    public function store(Request $r, Vehicle $vehicle)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'execution_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($r->hasFile('file')) {
            $data['file_path'] = $r->file('file')->store('vehicle_docs','public');
        }

        $doc = $vehicle->documents()->create($data);

        $this->logUserAction('audit','Doc veicolo creato',[
            'vehicle_id'=>$vehicle->id,
            'document_id'=>$doc->id
        ]);

        return response()->json($doc,201);
    }

    public function update(Request $r, VehicleDocument $vehicleDocument)
    {
        $data = $r->validate([
            'name'=>'string',
            'file'=>'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'execution_date'=>'nullable|date',
            'expiration_date'=>'nullable|date|after_or_equal:today',
        ]);

        if ($r->hasFile('file')) {
            // cancella vecchio se esiste
            if ($vehicleDocument->file_path) Storage::disk('public')->delete($vehicleDocument->file_path);
            $data['file_path'] = $r->file('file')->store('vehicle_docs','public');
        }

        $vehicleDocument->update($data);
        $this->logUserAction('audit','Doc veicolo aggiornato',['document_id'=>$vehicleDocument->id]);

        return $vehicleDocument;
    }

    public function destroy(VehicleDocument $vehicleDocument)
    {
        if ($vehicleDocument->file_path) Storage::disk('public')->delete($vehicleDocument->file_path);
        $vehicleDocument->delete();
        $this->logUserAction('audit','Doc veicolo eliminato',['document_id'=>$vehicleDocument->id]);
        return response()->noContent();
    }
}
