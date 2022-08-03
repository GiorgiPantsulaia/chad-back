<?php

namespace App\Http\Controllers;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
	public function index(): JsonResponse
	{
		return response()->json(GenreResource::collection(Genre::all()));
	}
}
