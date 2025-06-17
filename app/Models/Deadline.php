<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deadline extends Model
{

    use HasFactory;
    
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
