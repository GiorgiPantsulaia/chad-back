<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        $attributes = $request->validate(['name'=>'required|max:15|min:3|alpha_num|unique:users,name',
        'email'             => 'required|email|max:255|unique:users,email',
        'password'          => 'required|min:8|max:15|alpha_num',]);
        User::create($attributes);
        return "User Created Successfully.Waiting for Verification";
    }
}
