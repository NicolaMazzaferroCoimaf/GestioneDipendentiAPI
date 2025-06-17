<?php

namespace App\Models;

use App\Models\VehicleType;
use App\Models\VehicleDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{

    use HasFactory;     

    protected $fillable = ['vehicle_type_id','brand','model', 'license_plate','vin','registration_year'];

    public function type() { 
        return $this->belongsTo(VehicleType::class); 
    }
    public function documents() { 
        return $this->hasMany(VehicleDocument::class); 
    }
}
