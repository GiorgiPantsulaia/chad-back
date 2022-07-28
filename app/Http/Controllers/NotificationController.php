<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() :JsonResponse
    {
        return response()->json(Notification::where('recipient_id', auth()->user()->id)->with('sender', 'quote.movie')->get(), 200);
    }

    public function markAsRead(Request $request) :JsonResponse
    {
        Notification::where('id', $request->id)->update(['state'=>'read']);
        return response()->json('Notification marked as read', 200);
    }
    public function markAllRead() :JsonResponse
    {
        Notification::where('recipient_id', auth()->user()->id)->update(['state'=>'read']);
        
        return response()->json('Notifications marked as read', 200);
    }
}
