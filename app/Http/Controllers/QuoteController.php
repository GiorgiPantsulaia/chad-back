<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostLiked;
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

    public function likePost(Request $request): JsonResponse
    {
        $quote = Quote::where('id', $request->id)->with('likes')->first();

        $quote->likes()->attach(auth()->user());

        event(new PostLiked($quote));

        if ($quote->author->id!==auth()->user()->id) {
            Notification::create([
                'created_at'=>Carbon::now(),
                'user_id'=>auth()->user()->id,
                'type'=>'like',
                'state'=>'unread',
                'recipient_id'=>$quote->author->id,
                'quote_id'=>$quote->id,
            ]);

            $notification=Notification::where(['user_id'=>auth()->user()->id,
            'type'=>'like',
            'state'=>'unread',
            'recipient_id'=>$quote->author->id,
            'quote_id'=>$quote->id
        ])->with('sender')->with('post.movie')->latest()->first();
          
            event(new NewNotification($notification));
        }
        
        return response()->json(['success'=>'post has been liked'], 200);
    }
    public function unlikePost(Request $request): JsonResponse
    {
        $quote=Quote::where('id', $request->id)->with('likes')->first();
        $quote->likes()->detach(auth()->user());
        event(new PostLiked($quote));

        $notification=Notification::where(['user_id'=>auth()->user()->id,
        'type'=>'like',
        'state'=>'unread',
        'recipient_id'=>$quote->author->id,
        'quote_id'=>$quote->id
    ])->first();

        if ($notification) {
            $notification->delete();
        }

        return response()->json(['success'=>'post has been unliked'], 200);
    }
    public function create(Request $request): JsonResponse
    {
        $file = $request->file('img');
        $file_name=time(). '.' . $file->getClientOriginalName();
        $file->move(public_path('storage/quote-thumbnails'), $file_name);
        
        Quote::create([
            'body'=>[
                'en'=> $request->english_quote,
                'ka'=> $request->georgian_quote
            ],
            'user_id'=>auth()->user()->id,
            'movie_id'=>$request->movie_id,
            'thumbnail'=>'storage/quote-thumbnails/'.$file_name
            ]);
        return response()->json(['message'=>'Quote added successfully.'], 200);
    }

    public function destroy(Quote $quote): JsonResponse
    {
        $quote->delete();
        return response()->json(['message'=>'Quote deleted successfully.'], 200);
    }

    public function update(Request $request, Quote $quote) :JsonResponse
    {
        if ($request->file('img')) {
            $file = $request->file('img');
            $file_name=time(). '.' . $file->getClientOriginalName();
            $file->move(public_path('storage/quote-thumbnails'), $file_name);
            
            $quote->update([
                'body'=>[
                    'en'=>$request->english_quote,
                    'ka'=>$request->georgian_quote
                ],
                'thumbnail'=>'storage/quote-thumbnails/' . $file_name,
            ]);
            return response()->json(['message'=>'Quote updated successfully.'], 200);
        } else {
            $quote->update([
                'body'=>[
                    'en'=>$request->english_quote,
                    'ka'=>$request->georgian_quote
                ],
            ]);
            return response()->json(['message'=>'Quote updated successfully.'], 200);
        }
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
