<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index() :JsonResponse
    {
        return response()->json(Notification::where('recipient_id', auth()->user()->id)->with('sender')->with('post.movie')->get());
    }

    public function markAsRead(Request $request) :JsonResponse
    {
        Notification::where('id', $request->id)->update(['state'=>'read']);
        return response()->json('Notification marked as read');
    }
    public function markAllRead()
    {
        $notifications= Notification::where('recipient_id', auth()->user()->id)->get();
        foreach ($notifications as $notification) {
            $notification->update(['state'=>'read']);
        }
        return response()->json('Notifications marked as read');
    }
}
