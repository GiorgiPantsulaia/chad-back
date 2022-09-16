<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
	public function index(User $user): JsonResponse
	{
		return response()->json(['user'=> $user, 'receivedMessages'=>Message::where([['reciever_id', auth()->id()], ['sender_id', $user->id]])->get(),
			'sentMessages'                => Message::where([['sender_id', auth()->id()], ['reciever_id', $user->id]])->get(), ]);
	}

	public function store(Request $request, User $user): JsonResponse
	{
		$message = Message::create(['sender_id'=>auth()->id(), 'reciever_id'=>$user->id, 'body'=>$request->message]);
		event(new NewMessage($message));
		return response()->json($message);
	}
}
