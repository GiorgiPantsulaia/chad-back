<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
	public function redirect(): JsonResponse
	{
		$url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

		return response()->json(['url'=>$url], 200);
	}

	public function callback(): RedirectResponse
	{
		$googleUser = Socialite::driver('google')->stateless()->user();
		$user = User::firstWhere('email', $googleUser->getEmail());

		if (!$user)
		{
			$user = User::Create([
				'google_user'      => true,
				'name'             => $googleUser->getName(),
				'email'            => $googleUser->getEmail(),
				'password'         => Hash::make($googleUser->getName() . '@' . $googleUser->getId()),
				'email_verified_at'=> now(),
			]);
		}
		$token = auth('api')->login($user);
		$expires_in = auth('api')->factory()->getTTL() * 60;
		$username = auth('api')->user()->name;
		$user_email = auth('api')->user()->email;
		$user_pfp = auth('api')->user()->profile_pic;
		$user_id = auth('api')->user()->id;
		return redirect()
		->away(env('FRONT_REDIRECT') . "/redirecting?token={$token}&expires_in={$expires_in}&username={$username}&email={$user_email}&user_pfp={$user_pfp}&user_id={$user_id}");
	}
}
