<?php

namespace App\Http\Controllers\Api;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleTypeController extends Controller
{
    public function index()  { 
        return VehicleType::all(); 
    }

    public function store(Request $r)
    {
        $data = $r->validate(['name'=>'required|unique:vehicle_types,name']);
        return VehicleType::create($data);
    }

    public function update(Request $r, VehicleType $vehicleType)
    {
        $data = $r->validate(['name'=>"required|unique:vehicle_types,name,{$vehicleType->id}"]);
        $vehicleType->update($data);
        return $vehicleType;
    }

    public function destroy(VehicleType $vehicleType)
    {
        $vehicleType->delete();
        return response()->noContent();
    }
}
