<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Movie extends Model
{
    protected $guarded = ['id'];
    
    use HasFactory,HasTranslations;

    public $translatable = ['title','director','description'];

    public function quotes() : HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function author() :BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function genres() :BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }
}
