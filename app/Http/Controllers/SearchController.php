<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\QuoteResource;
use App\Models\Movie;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$search = substr($request->search, 1);
		if ($request->search[0] === '@')
		{
			$search = substr($request->search, 1);
			$movies = Movie::where(strtolower('title->en'), 'like', '%' . $search . '%')
			->orWhere(strtolower('title->ka'), 'like', '%' . $search . '%')->get();
			return response()->json(['movies'=> MovieResource::collection($movies->load('quotes'))], 200);
		}
		elseif ($request->search[0] === '#')
		{
			$quotes = Quote::where('body->en', 'like', '%' . $search . '%')
			->orWhere('body->ka', 'like', '%' . $search . '%')->get();
			return response()->json(['quotes'=>QuoteResource::collection($quotes->load('author', 'movie', 'likes', 'comments'))], 200);
		}
		else
		{
			return response()->json(['error'=> 'wrong search keyword'], 422);
		}
	}
}
