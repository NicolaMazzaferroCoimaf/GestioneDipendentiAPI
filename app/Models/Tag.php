<?php

namespace App\Models;

use App\Models\Deadline;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function deadlines()
    {
        return $this->belongsToMany(Deadline::class);
    }
}
