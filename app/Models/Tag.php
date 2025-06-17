<?php

namespace App\Models;

use App\Models\Deadline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{

    use HasFactory;

    protected $fillable = ['name'];

    public function deadlines()
    {
        return $this->belongsToMany(Deadline::class);
    }
}
