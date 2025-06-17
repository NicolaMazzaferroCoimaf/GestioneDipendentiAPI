<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presence extends Model
{

    use HasFactory;

    protected $fillable = ['employee_id', 'type', 'start_date', 'end_date', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
