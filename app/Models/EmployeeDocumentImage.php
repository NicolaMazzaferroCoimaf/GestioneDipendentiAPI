<?php

namespace App\Models;

use App\Models\EmployeeDocument;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocumentImage extends Model
{
    protected $fillable = ['employee_document_id', 'path'];

    public function employeeDocument()
    {
        return $this->belongsTo(EmployeeDocument::class);
    }
}
