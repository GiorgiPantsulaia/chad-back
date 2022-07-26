<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilePicRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\NewEmailConfirmation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    private static function sendConfirmation(string $name, mixed $email, mixed $verification_code)
    {
        $data = [
            'name'              => $name,
            'email'=>$email,
            'verification_code' => $verification_code,
        ];
        Mail::to($email)->send(new NewEmailConfirmation($data));
    }
    public function index(): JsonResponse
    {
        return response()->json(['user'=>auth()->user()]);
    }
    public function update(UpdateUserRequest $request)
    {
        $confirmation_sent=false;
        if ($request->img) {
            $file = $request->file('img');
            $file_name=time(). '.' . $file->getClientOriginalName();
            $file->move(public_path('storage/profile-pictures'), $file_name);
            User::where('email', $request->user_email)->update(['profile_pic'=>'storage/profile-pictures/'.$file_name]);
        }
        if ($request->name) {
            User::where('email', $request->user_email)->update(['name'=>$request->name]);
        }
        if ($request->email) {
            if (User::where('email', $request->email)->first()) {
                return response()->json('User with this email already exists', 409);
            } else {
                $user=User::where('email', $request->user_email)->first();
                $user->verification_code=sha1(time());
                $user->save();
                $this->sendConfirmation($user->name, $request->email, $user->verification_code);
                $confirmation_sent=true;
            }
        }
        if ($request->password) {
            User::where('email', $request->user_email)->update(['password'=>Hash::make($request->password) ]);
        }
        return response()->json(['message'=>'Profile updated successfully',
        'user'=> User::where('email', $request->user_email)->first(),'confirmation_sent'=>$confirmation_sent], 200);
    }
    public function updateEmail(Request $request)
    {
        User::where('verification_code', $request->verification_code)->update(['email'=>$request->email]);
        return response()->json('Email updated successfully', 200);
    }
}
