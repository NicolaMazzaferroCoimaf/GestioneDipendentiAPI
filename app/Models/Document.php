<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{

    use HasFactory;  
    
    protected $fillable = ['name'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_document');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_documents')
                    ->withPivot('expiration_date')
                    ->withTimestamps();
    }
}