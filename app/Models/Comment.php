<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comment extends Model
{
	use HasFactory;

	protected $guarded = ['id'];

	public function quote(): BelongsTo
	{
		return $this->belongsTo(Quote::class, 'quote_id');
	}

	public function author(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	// public function likes(): BelongsToMany
	// {
	// 	return $this->belongsToMany(User::class);
	// }
}
