<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{

    use HasFactory;
    
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
