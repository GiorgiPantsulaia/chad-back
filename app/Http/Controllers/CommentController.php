<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostCommented;
use App\Events\CommentDeleted;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\NotificationRequests\CommentNotificationRequest;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
	public function create(CreateCommentRequest $request, CommentNotificationRequest $notificationRequest): JsonResponse
	{
		DB::transaction(
			function () use ($request, $notificationRequest) {
				$comment = Comment::create($request->validated());

				event(new PostCommented($comment->load('author')));

				if ($request->recipient_id !== auth()->user()->id)
				{
					$notification = Notification::create($notificationRequest->validated());
					event(new NewNotification($notification->load('sender', 'quote.movie')));
				}
			}
		);
		return response()->json(['message'=>'Comment added successfully'], 200);
	}

	public function destroy(Comment $comment)
	{
		event(new CommentDeleted($comment));

		$comment->delete();

		return response()->json(['message'=>'Comment deleted successfully'], 200);
	}
}
