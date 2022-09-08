<?php

namespace App\Http\Controllers;

use App\Models\User;

class FriendController extends Controller
{
	public function store(User $user)
	{
		$user = auth()->user();
		// $user->friendsTo()->attach($user);

		return response()->json('Friend request sent successfully.');
	}
}
