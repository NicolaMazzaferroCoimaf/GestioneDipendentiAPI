<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'name',
        'file_path',
        'execution_date',
        'expiration_date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
