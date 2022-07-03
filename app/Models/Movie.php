<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $guarded = ['id'];
    
    use HasFactory;

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
