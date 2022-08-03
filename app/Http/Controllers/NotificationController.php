<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
	public function index(): JsonResponse
	{
		$notifications = Notification::where('recipient_id', auth()->user()->id)->get();
		return response()->json(NotificationResource::collection($notifications), 200);
	}

	public function markAsRead(Notification $notification): JsonResponse
	{
		$notification->update(['state'=>'read']);
		return response()->json(['message'=>'Notification marked as read'], 200);
	}

	public function markAllRead(): JsonResponse
	{
		Notification::where('recipient_id', auth()->user()->id)->update(['state'=>'read']);

		return response()->json(['message'=>'Notifications marked as read'], 200);
	}
}
