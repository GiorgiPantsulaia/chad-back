<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Staudenmeir\LaravelMergedRelations\Eloquent\HasMergedRelationships;

class User extends Authenticatable implements JWTSubject
{
	use HasApiTokens, HasFactory, Notifiable, HasMergedRelationships;

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

	public function friendsTo()
	{
		return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
		 ->withPivot('accepted');
	}

	public function friendsFrom()
	{
		return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
		 ->withPivot('accepted');
	}

	public function friendRequestSent()
	{
		return $this->friendsTo()->wherePivot('accepted', false);
	}

	public function friendRequestReceived()
	{
		return $this->friendsFrom()->wherePivot('accepted', false);
	}

	public function acceptedOngoingRequest()
	{
		return $this->friendsTo()->wherePivot('accepted', true);
	}

	public function acceptedIncomingRequest()
	{
		return $this->friendsFrom()->wherePivot('accepted', true);
	}

	public function friends()
	{
		return $this->mergedRelationWithModel(User::class, 'friends_view');
	}
}
