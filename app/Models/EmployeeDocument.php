<?php

namespace App\Models;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model
{

    use HasFactory;
    
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
