<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Http\Requests\NotificationRequests\FriendNotificationRequest;
use App\Http\Resources\UserResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json(UserResource::collection(auth()->user()->getFriends()));
	}

	public function store(FriendNotificationRequest $request, User $user): JsonResponse
	{
		$authUser = auth()->user();
		$authUser->befriend($user);
		$notification = Notification::create($request->validated()
		+ [
			'recipient_id'=> $user->id,
			'quote_id'    => null,
		]);

		event(new NewNotification($notification->load('sender')));
		return response()->json('Friend request sent successfully.');
	}

	public function acceptFriend(Request $request, User $user): JsonResponse
	{
		auth()->user()->acceptFriendRequest($user);

		$notification = Notification::find($request->notificationId);
		$notification->update(['state'=>'read', 'status'=>'accepted']);

		return response()->json('Friend request accepted successfully.');
	}
}
