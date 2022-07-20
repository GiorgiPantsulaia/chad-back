<?php

namespace App\Http\Controllers;

use App\Events\PostLiked;
use App\Models\Comment;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index(): JsonResponse
    {
        return response()->json(['data'=>Quote::latest()->with('movie')->with('author')->with('comments.author')->paginate(5)]) ;
    }

    public function likePost(Request $request): JsonResponse
    {
        $quote = Quote::where('id', $request->id)->with('movie')->with('author')->with('comments.author')->first();
        $quote->update(['likes_number'=> DB::raw('likes_number+1'), ]);
        event(new PostLiked($quote));
        $quote->likes()->attach(auth()->user());
        return response()->json(['success'=>'post has been liked'], 200);
    }
    public function unlikePost(Request $request): JsonResponse
    {
        Quote::where('id', $request->id)->update(['likes_number'=> DB::raw('likes_number-1'), ]);
        return response()->json(['success'=>'post has been unliked'], 200);
    }
    public function create(Request $request): JsonResponse
    {
        $file = $request->file('img');
        $file_name=time(). '.' . $file->getClientOriginalName();
        $file->move(public_path('quote-thumbnails'), $file_name);
        
        Quote::create([
            'body'=>[
                'en'=> $request->english_quote,
                'ka'=> $request->georgian_quote
            ],
            'user_id'=>auth()->user()->id,
            'movie_id'=>$request->movie_id,
            'likes_number'=>0,
            'thumbnail'=>'quote-thumbnails/'.$file_name
            ]);
        return response()->json(['message'=>'Quote added successfully.']);
    }

    public function destroy(Request $request): JsonResponse
    {
        Quote::where('id', $request->id)->delete();
        return response()->json(['message'=>'Quote deleted successfully.']);
    }

    public function update(Request $request)
    {
        if ($request->file('img')) {
            $file = $request->file('img');
            $file_name=time(). '.' . $file->getClientOriginalName();
            $file->move(public_path('quote-thumbnails'), $file_name);
            Quote::where('id', $request->id)->update([
                'body'=>[
                    'en'=>$request->english_quote,
                    'ka'=>$request->georgian_quote
                ],
                'thumbnail'=>'quote-thumbnails/' . $file_name,
            ]);
            return response()->json(['message'=>'Quote updated successfully.']);
        } else {
            Quote::where('id', $request->id)->update([
                'body'=>[
                    'en'=>$request->english_quote,
                    'ka'=>$request->georgian_quote
                ],
            ]);
            return response()->json(['message'=>'Quote updated successfully.']);
        }
    }
}
