<?php

namespace App\Models;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleType extends Model
{

    use HasFactory;

    protected $fillable = ['name'];

    public function vehicles() { 
        return $this->hasMany(Vehicle::class); 
    }
}
