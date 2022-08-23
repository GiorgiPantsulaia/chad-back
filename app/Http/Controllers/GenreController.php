<?php

namespace App\Http\Controllers;

use App\Http\Resources\GenreResource;
use App\Http\Resources\MovieResource;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json(GenreResource::collection(Genre::all()));
	}

	public function show(Request $request)
	{
		$genre = Genre::firstWhere('title->en', $request->genre);
		return response()->json(MovieResource::collection($genre->movies->load('quotes')));
	}
}
