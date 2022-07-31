<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostLiked;
use App\Http\Requests\NotificationRequests\CreateLikeNotificationRequest;
use App\Http\Requests\QuoteRequests\CreateQuoteRequest;
use App\Http\Requests\QuoteRequests\UpdateQuoteRequest;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json(Quote::latest()->with('movie', 'author', 'comments.author', 'likes')->paginate(5), 200);
	}

	public function likePost(Quote $quote, CreateLikeNotificationRequest $request): JsonResponse
	{
		DB::transaction(
			function () use ($quote, $request) {
				$quote->likes()->attach(auth()->user());
				event(new PostLiked($quote->load('likes')));

				if ($quote->author->id !== auth()->user()->id)
				{
					$attributes = $request->validated();
					$attributes['recipient_id'] = $quote->author->id;
					$attributes['quote_id'] = $quote->id;
					$notification = Notification::create($attributes);

					event(new NewNotification($notification->load('sender', 'quote.movie')));
				}
			}
		);
		return response()->json(['success'=>true], 200);
	}

	public function unlikePost(Quote $quote): JsonResponse
	{
		DB::transaction(
			function () use ($quote) {
				$quote->likes()->detach(auth()->user());
				event(new PostLiked($quote->load('likes')));

				$notification = Notification::firstWhere([
					'user_id'     => auth()->user()->id,
					'type'        => 'like',
					'state'       => 'unread',
					'recipient_id'=> $quote->author->id,
					'quote_id'    => $quote->id,
				]);

				if ($notification)
				{
					$notification->delete();
				}
			}
		);

		return response()->json(['success'=>true], 200);
	}

	public function create(CreateQuoteRequest $request): JsonResponse
	{
		$attributes = $request->validated();
		$file = $request->file('img');
		$file_name = time() . '.' . $file->getClientOriginalName();
		$file->move(public_path('storage/quote-thumbnails'), $file_name);
		$attributes['thumbnail'] = 'storage/quote-thumbnails/' . $file_name;

		Quote::create($attributes);
		return response()->json(['message'=>'Quote added successfully.'], 201);
	}

	public function destroy(Quote $quote): JsonResponse
	{
		$quote->delete();
		return response()->json(['message'=>'Quote deleted successfully.'], 200);
	}

	public function update(UpdateQuoteRequest $request, Quote $quote): JsonResponse
	{
		$quote->update($request->validated());

		return response()->json(['message'=>'Quote updated successfully.'], 200);
	}

	public function show(Quote $quote): JsonResponse
	{
		return response()->json($quote->load(['author', 'movie', 'comments.author', 'likes']), 200);
	}
}
