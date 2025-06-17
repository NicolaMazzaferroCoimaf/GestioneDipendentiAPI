<?php

namespace App\Http\Controllers\Api;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleController extends Controller
{
    use LogsUserAction;

    public function index()
    {
        return Vehicle::with('type','documents')->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'vehicle_type_id'=>'required|exists:vehicle_types,id',
            'brand'=>'required|string',
            'model'=>'required|string',
            'license_plate'=>'required|string|unique:vehicles,license_plate',
            'vin'=>'required|string|unique:vehicles,vin',
            'registration_year'=>'required|digits:4|integer|min:1980|max:'.now()->year,
        ]);
        $v = Vehicle::create($data);
        $this->logUserAction('audit','Creato veicolo',['vehicle_id'=>$v->id]);
        return $v;
    }

    public function show(Vehicle $vehicle)
    {
        return $vehicle->load('type','documents');
    }

    public function update(Request $r, Vehicle $vehicle)
    {
        $data = $r->validate([
            'vehicle_type_id'=>'exists:vehicle_types,id',
            'brand'=>'string',
            'model'=>'string',
            'license_plate'=>"string|unique:vehicles,license_plate,{$vehicle->id}",
            'vin'=>"string|unique:vehicles,vin,{$vehicle->id}",
            'registration_year'=>'digits:4|integer|min:1980|max:'.now()->year,
        ]);
        $vehicle->update($data);
        $this->logUserAction('audit','Aggiornato veicolo',['vehicle_id'=>$vehicle->id]);
        return $vehicle;
    }

    public function destroy(Vehicle $vehicle)
    {
        $id=$vehicle->id; $vehicle->delete();
        $this->logUserAction('audit','Eliminato veicolo',['vehicle_id'=>$id]);
        return response()->json(['message'=>'Veicolo eliminato']);
    }
}
