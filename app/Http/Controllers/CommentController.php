<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Quote;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function addComment(Request $request)
    {
        Comment::create(['user_id'=>auth()->user()->id,'quote_id'=>$request->quote_id,'body'=>$request->body]);
        return response()->json(['message'=>'Comment added successfully','data'=>Quote::latest()->with('movie')->with('author')->with('comments.author')->paginate(5)]);
    }
}
