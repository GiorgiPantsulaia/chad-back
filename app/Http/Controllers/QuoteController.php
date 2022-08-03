<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Events\PostLiked;
use App\Http\Requests\NotificationRequests\CreateLikeNotificationRequest;
use App\Http\Requests\QuoteRequests\CreateQuoteRequest;
use App\Http\Requests\QuoteRequests\UpdateQuoteRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json(QuoteResource::collection(Quote::latest()->paginate(5))->response()->getData(true), 200);
	}

	public function likePost(CreateLikeNotificationRequest $request, Quote $quote): JsonResponse
	{
		DB::transaction(
			function () use ($quote, $request) {
				$quote->likes()->attach(auth()->user());
				event(new PostLiked($quote->load('likes')));

				if ($quote->author->id !== auth()->user()->id)
				{
					$notification = Notification::create($request->validated()
					+ [
						'recipient_id'=> $quote->author->id,
						'quote_id'    => $quote->id,
					]);

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

				Notification::firstWhere([
					'user_id'     => auth()->user()->id,
					'type'        => 'like',
					'recipient_id'=> $quote->author->id,
					'quote_id'    => $quote->id,
				])?->delete();
			}
		);

		return response()->json(['success'=>true], 200);
	}

	public function create(CreateQuoteRequest $request): JsonResponse
	{
		$file = $request->file('img');
		$file_name = time() . '.' . $file->getClientOriginalName();
		$file->move(public_path('storage/quote-thumbnails'), $file_name);

		Quote::create($request->validated() + ['thumbnail'=>'storage/quote-thumbnails/' . $file_name]);
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
		return response()->json(new QuoteResource($quote), 200);
	}
}
