<?php

namespace App\Models;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name'];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'group_document');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_group');
    }
}