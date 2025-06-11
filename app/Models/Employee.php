<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Document;
use App\Models\EmployeeDocument;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'surname', 'email', 'phone'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'employee_group');
    }

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'employee_documents')
                    ->withPivot('expiration_date')
                    ->withTimestamps();
    }
}
