<?php

use App\Models\Notification;
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
    if ($recipient_id==auth()->user()->id) {
        return true;
    }
});
