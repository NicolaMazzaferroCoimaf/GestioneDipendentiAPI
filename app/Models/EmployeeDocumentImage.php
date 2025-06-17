<?php

namespace App\Models;

use App\Models\EmployeeDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocumentImage extends Model
{

    use HasFactory;
    
    protected $fillable = ['employee_document_id', 'path'];

    public function employeeDocument()
    {
        return $this->belongsTo(EmployeeDocument::class);
    }
}
