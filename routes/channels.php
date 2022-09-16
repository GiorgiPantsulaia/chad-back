<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('notification.{recipient_id}', function ($user, $recipient_id) {
	if ((int)$recipient_id === auth()->user()->id)
	{
		return true;
	}
});
Broadcast::channel('messages.{sender_id}.{reciever_id}', function ($user, $sender_id, $reciever_id) {
	return(int)$reciever_id === auth()->id();
});
