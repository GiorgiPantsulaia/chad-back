<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequests\CreateMovieRequest;
use App\Http\Requests\MovieRequests\UpdateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
	private function slugify(string $string): string
	{
		return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
	}

	public function index(): JsonResponse
	{
		$movies = Movie::latest()->where('user_id', auth()->user()->id)->with('quotes')->get();
		return response()->json(MovieResource::collection($movies), 200);
	}

	public function store(CreateMovieRequest $request): JsonResponse
	{
		DB::transaction(
			function () use ($request) {
				$file = $request->file('img');
				$file_name = time() . '.' . $file->getClientOriginalName();
				$file->move(public_path('storage/movie-thumbnails'), $file_name);

				$movie = Movie::create($request->validated()
			+ [
				'slug'      => $this->slugify($request->english_title),
				'thumbnail' => 'storage/movie-thumbnails/' . $file_name,
			]);

				$genres = Genre::whereIn('title->' . $request->lang, explode(',', $request->chosen_genres))->get();
				$movie->genres()->attach($genres);
			}
		);

		return response()->json(['message'=>'Movie added successfully.'], 201);
	}

	public function show(Request $request): JsonResponse
	{
		$movie = Movie::firstWhere('slug', $request->slug);

		return response()->json(new MovieResource($movie->load(['quotes'=>['comments', 'likes', 'author'], 'genres'])), 200);
	}

	public function destroy(Movie $movie): JsonResponse
	{
		if ($movie->user_id === auth()->id())
		{
			$movie->delete();
			return response()->json(['message'=>'Movie deleted successfully.'], 200);
		}
		return response()->json(['message'=>'You do not have permission to delete this movie']);
	}

	public function update(UpdateMovieRequest $request, Movie $movie): JsonResponse
	{
		DB::transaction(
			function () use ($request, $movie) {
				if ($request->img)
				{
					$file = $request->file('img');
					$file_name = time() . '.' . $file->getClientOriginalName();
					$file->move(public_path('storage/movie-thumbnails'), $file_name);
					$movie->update(['thumbnail'=>'storage/movie-thumbnails/' . $file_name]);
				}
				if ($request->chosen_genres)
				{
					$genres = Genre::whereIn('title->' . $request->lang, explode(',', $request->chosen_genres))->get();
					$movie->genres()->detach();
					$movie->genres()->attach($genres);
				}
				$attributes = $request->validated();
				if ($request->english_title)
				{
					$attributes['slug'] = $this->slugify($request->english_title);
				}
				$movie->update($attributes);
				return $attributes;
			}
		);
		return response()->json(['message'=>'Movie updated successfully'], 200);
	}
}
