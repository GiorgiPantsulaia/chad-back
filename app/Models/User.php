<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'google_user',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function setPasswordAttribute($password)
	{
		$this->attributes['password'] = bcrypt($password);
	}

	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	public function getJWTCustomClaims(): array
	{
		return [];
	}

	public function movies(): HasMany
	{
		return $this->hasMany(Movie::class);
	}

	public function notifications(): HasMany
	{
		return $this->hasMany(Notification::class);
	}

	public function liked_posts(): BelongsToMany
	{
		return $this->belongsToMany(Quote::class);
	}

	// public function liked_comments(): BelongsToMany
	// {
	// 	return $this->belongsToMany(Comment::class);
	// }
	public function friends(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'user_user', 'first_user_id', 'second_user_id');
	}
}
