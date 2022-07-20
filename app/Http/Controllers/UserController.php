<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index(): JsonResponse
    {
        return response()->json(['user'=>auth()->user()]);
    }
    public function likedPosts()
    {
        return response()->json(['data'=>auth()->user()->liked_posts]);
    }
}
