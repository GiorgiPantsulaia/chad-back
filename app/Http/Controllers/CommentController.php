<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostCommented;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function addComment(Request $request): JsonResponse
    {
        Comment::create(['user_id'=>auth()->user()->id,'quote_id'=>$request->quote_id,'body'=>$request->body]);

        $comment = Comment::where(['user_id'=>auth()->user()->id,'body'=>$request->body,'quote_id'=>$request->quote_id])->with('author')->first();
        
        event(new PostCommented($comment));
        
        if ($request->author_id!==auth()->user()->id) {
            Notification::create([
            'created_at'=>Carbon::now(),
            'user_id'=>auth()->user()->id,
            'type'=>'comment',
            'state'=>'unread',
            'recipient_id'=>$request->author_id,
            'quote_id'=>$request->quote_id,
        ]);
        
            $notification=Notification::where(['user_id'=>auth()->user()->id,
        'type'=>'comment',
        'state'=>'unread',
        'recipient_id'=>$request->author_id,
        'quote_id'=>$request->quote_id,
        ])->with('sender')->with('post.movie')->latest()->first();
        
            event(new NewNotification($notification));
        }
        return response()->json(['message'=>'Comment added successfully'], 200);
    }
}
