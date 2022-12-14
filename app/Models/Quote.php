<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory,HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['body'];

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function movie(): BelongsTo
	{
		return $this->belongsTo(Movie::class, 'movie_id');
	}

	public function author(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function likes(): BelongsToMany
	{
		return $this->belongsToMany(User::class);
	}
}
