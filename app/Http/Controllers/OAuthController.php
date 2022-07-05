<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirect()
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

        return response()->json(['url'=>$url]);
    }
    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::Create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password'=>Hash::make($googleUser->name . '@' . $googleUser->id),
            ]);
        }
        $token = auth('api')->login($user);
        $expires_in = auth('api')->factory()->getTTL() * 60;
        return redirect()->away("http://localhost:3000/redirecting?token={$token}&expires_in={$expires_in}");
    }
}
