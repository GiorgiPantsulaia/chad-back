<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\NewEmailConfirmation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
	private static function sendConfirmation(string $name, mixed $email, mixed $verification_code): void
	{
		$data = [
			'name'              => $name,
			'email'             => $email,
			'verification_code' => $verification_code,
		];
		Mail::to($email)->send(new NewEmailConfirmation($data));
	}

	public function update(UpdateUserRequest $request, User $user): JsonResponse
	{
		$confirmation_sent = false;
		if ($request->img)
		{
			$file = $request->file('img');
			$file_name = time() . '.' . $file->getClientOriginalName();
			$file->move(public_path('storage/profile-pictures'), $file_name);
			$user->profile_pic = 'storage/profile-pictures/' . $file_name;
			$user->save();
		}

		if ($request->name)
		{
			$user->update(['name'=>$request->name]);
		}
		if ($request->email)
		{
			if (User::firstWhere('email', $request->email))
			{
				return response()->json(['error'=> 'User with this email already exists'], 409);
			}
			else
			{
				$user->verification_code = sha1(time());
				$user->save();
				$this->sendConfirmation($user->name, $request->email, $user->verification_code);
				$confirmation_sent = true;
			}
		}
		if ($request->password)
		{
			$user->update(['password'=>$request->password]);
		}
		return response()->json([
			'message'          => 'Profile updated successfully',
			'user'             => $user,
			'confirmation_sent'=> $confirmation_sent, ], 200);
	}

	public function updateEmail(Request $request): JsonResponse
	{
		User::where('verification_code', $request->verification_code)->update(['email'=>$request->email]);
		return response()->json(['message'=> 'Email updated successfully'], 200);
	}

	public function show(User $user): JsonResponse
	{
		// find out friendship status for more flexibility (pending,friend or whatever)
		return response()->json(['user'=>new UserResource($user->load('likedPosts')), 'friend'=>auth()->user()->isFriendWith($user)]);
	}
}
