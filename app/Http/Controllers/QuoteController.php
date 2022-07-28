<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostLiked;
use App\Http\Requests\CreateLikeNotificationRequest;
use App\Http\Requests\CreateQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Quote::latest()->with('movie')->with('author')->with('comments.author')->with('likes')->paginate(5), 200) ;
    }

    public function likePost(Quote $quote, CreateLikeNotificationRequest $request): JsonResponse
    {
        $quote->likes()->attach(auth()->user());
        event(new PostLiked($quote->load('likes')));

        if ($quote->author->id!==auth()->user()->id) {
            $attributes = $request->validated();
            $attributes['recipient_id']=$quote->author->id;
            $attributes['quote_id']=$quote->id;
            $notification=Notification::create($attributes);

            event(new NewNotification($notification->load('sender', 'quote.movie')));
        }
        
        return response()->json(['success'=>'post has been liked'], 200);
    }
    public function unlikePost(Quote $quote): JsonResponse
    {
        $quote->likes()->detach(auth()->user());
        event(new PostLiked($quote->load('likes')));

        $notification=Notification::firstWhere(['user_id'=>auth()->user()->id,
        'type'=>'like',
        'state'=>'unread',
        'recipient_id'=>$quote->author->id,
        'quote_id'=>$quote->id
    ]);

        if ($notification) {
            $notification->delete();
        }

        return response()->json(['success'=>'post has been unliked'], 200);
    }
    public function create(CreateQuoteRequest $request): JsonResponse
    {
        Quote::create($request->validated());
        return response()->json(['message'=>'Quote added successfully.'], 200);
    }

    public function destroy(Quote $quote): JsonResponse
    {
        $quote->delete();
        return response()->json(['message'=>'Quote deleted successfully.'], 200);
    }

    public function update(UpdateQuoteRequest $request, Quote $quote) :JsonResponse
    {
        $quote->update($request->validated());
          
        return response()->json(['message'=>'Quote updated successfully.'], 200);
    }
    public function show(Quote $quote) :JsonResponse
    {
        if ($quote) {
            return response()->json($quote->load(['author', 'movie', 'comments.author', 'likes']), 200);
        } else {
            return response()->json('error', 404);
        }
    }
}
