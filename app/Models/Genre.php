<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Genre extends Model
{
    protected $fillable = ['title'];

    use HasFactory,HasTranslations;

    public $translatable = ['title'];


    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}
