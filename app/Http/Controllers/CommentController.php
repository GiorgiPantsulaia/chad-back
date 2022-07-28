<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostCommented;
use App\Http\Requests\CreateCommentNotificationRequest;
use App\Http\Requests\CreateCommentRequest;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function create(CreateCommentRequest $request, CreateCommentNotificationRequest $notification_request): JsonResponse
    {
        Comment::create($request->validated());

        $comment = Comment::where(['user_id'=>auth()->user()->id,'body'=>$request->body,'quote_id'=>$request->quote_id])->with('author')->first();
        
        event(new PostCommented($comment));
        
        if ($request->recipient_id!==auth()->user()->id) {
            $notification=Notification::create($notification_request->validated());
            event(new NewNotification($notification->load('sender', 'quote.movie')));
        }
        return response()->json(['message'=>'Comment added successfully'], 200);
    }
}
