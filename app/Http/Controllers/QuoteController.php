<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        return response()->json(['data'=>Quote::latest()->with('movie')->with('author')->with('comments.author')->paginate(5)]) ;
    }

    public function likePost(Request $request)
    {
        Quote::where('id', $request->id)->update(['likes_number'=> DB::raw('likes_number+1'), ]);
        return response()->json(['success'=>'post has been liked'], 200);
    }
    public function unlikePost(Request $request)
    {
        Quote::where('id', $request->id)->update(['likes_number'=> DB::raw('likes_number-1'), ]);
        return response()->json(['success'=>'post has been unliked'], 200);
    }
}
