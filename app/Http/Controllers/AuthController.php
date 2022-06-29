<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create(RegisterRequest $request)
    {
        $attributes = $request->validated();
        $user = User::create($attributes);
        return response()->json(['success' => true, 'user_id' => $user->id], 200);
    }
    public function login(Request $request)
    {
        $login = $request->name;

        $nameOrEmail = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $request->merge([$nameOrEmail => $login]);

        $credentials = $request->only([$nameOrEmail, 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Email or password is incorrect,check your credentials.'], 401);
        } elseif (Auth::attempt($credentials)) {
            return $this->respondWithToken($token, $request);
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'user_id' => auth()->user()->id,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
