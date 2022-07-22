<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Mail\EmailConfirmation;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private static function sendVerification(string $name, mixed $email, mixed $verification_code)
    {
        Mail::to($email)->send(new EmailVerification([
            'name'              => $name,
            'verification_code' => $verification_code,
        ]));
    }
    private static function sendConfirmation(string $name, mixed $email, mixed $verification_code)
    {
        $data = [
            'name'              => $name,
            'verification_code' => $verification_code,
        ];
        Mail::to($email)->send(new EmailConfirmation($data));
    }
    public function create(RegisterRequest $request) : JsonResponse
    {
        $attributes = $request->validated();
        $user = User::create($attributes);
        $user->verification_code = sha1(time());
        $user->save();
        if ($user != null) {
            $this->sendVerification($user->name, $user->email, $user->verification_code);
            return response()->json(['message'=>'Verification Email Sent!'], 200);
        }
    }
    public function login(Request $request) : JsonResponse
    {
        $login = $request->name;

        $nameOrEmail = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $request->merge([$nameOrEmail => $login]);

        $credentials = $request->only([$nameOrEmail, 'password']);

        $user = User::where('email', $request->name)->orWhere('name', $request->name)->first();
        if ($user && $user->email_verified_at===null) {
            return response()->json(['error'=>'Check your email to activate account.']);
        } elseif (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'incorrect credentials'], 401);
        } elseif (Auth::attempt($credentials)) {
            return $this->respondWithToken($token, $request);
        }
    }
    protected function respondWithToken(string $token) :JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'username'=>auth()->user()->name,
            'user_email'=>auth()->user()->email,
            'user_id'=>auth()->user()->id,
            'user_pfp'=>auth()->user()->profile_pic
        ]);
    }
    public function logout() : JsonResponse
    {
        auth()->logout(true);
        return response()->json(['message'=>"Logged out successfully.auth token invalidated."]) ;
    }
    public function verifyEmail(Request $request) : JsonResponse
    {
        $verification_code = $request->token;
        $user = User::where('verification_code', $verification_code)->first();

        if ($user !== null) {
            $user->markEmailAsVerified();
            return response()->json(['message'=>'Email Activated Successfully'], 200);
        }
    }
    public function confirmEmail(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();

        if ($user) {
            $this->sendConfirmation($user->name, $user->email, $user->verification_code);
            return response()->json(['message'=>'Email confirmation sent.'], 200);
        } else {
            return response()->json(['error'=>'User does not exist'], 422);
        }
    }
    public function resetPassword(Request $request)
    {
        $verification_code=$request->code;
        $user=User::where('verification_code', $verification_code)->first();
        $user->password=$request->password;
        $user->save();

        return response()->json(['message'=>'Password updated successfully.'], 200);
    }
}
