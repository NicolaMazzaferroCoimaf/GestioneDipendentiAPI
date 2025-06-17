<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    protected $fillable = ['title','description','expiration_date','file_path'];

    public function tags()
    { 
        return $this->belongsToMany(Tag::class); 
    }

    public function scopeExpired ($q)
    { 
        return $q->whereDate('expiration_date','<', now());
    }

    public function scopeExpiring($q,$days){ 
        return $q->whereBetween('expiration_date', [now(), now()->addDays($days)]); 
    }
}
