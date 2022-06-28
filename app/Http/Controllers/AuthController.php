<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }
    public function create(RegisterRequest $request)
    {
        $attributes = $request->validated();
        $user=User::create($attributes);
        return ['success'=>true,'user_id'=>$user->id];
    }
    public function login(Request $request)
    {
        $login = $request->name;

		$nameOrEmail = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

		$request->merge([$nameOrEmail => $login]);

        $credentials = $request->only([$nameOrEmail, 'password']);
        
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return $this->respondWithToken($token,$request);
    }
    protected function respondWithToken($token,$request)
    {
        $user = User::where('name', 'LIKE', "%{$request->name}%") 
        ->orWhere('email', 'LIKE', "%{$request->name}%")
        ->first();
        return response()->json([
            'user_id'=>$user->id,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
