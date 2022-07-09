<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        $user = auth()->user();
        return response()->json(['data'=>Movie::where('user_id', $user->id)->with('quotes')->get()]);
    }
}
