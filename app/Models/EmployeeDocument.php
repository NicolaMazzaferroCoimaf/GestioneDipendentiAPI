<?php

namespace App\Models;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = ['employee_id', 'document_id', 'expiration_date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function images()
    {
        return $this->hasMany(EmployeeDocumentImage::class);
    }

}
