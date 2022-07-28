<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    private function slugify(string $string) :string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $movies=Movie::latest()->where('user_id', $user->id)->with('quotes')->get();
        return response()->json($movies, 200);
    }
    public function create(Request $request): JsonResponse
    {
        $file = $request->file('img');
        $file_name=time(). '.' . $file->getClientOriginalName();
        $file->move(public_path('storage/movie-thumbnails'), $file_name);
        $slug = $this->slugify($request->english_title);
        
        $movie=Movie::create([
            'title'=>[
                'en'=> $request->english_title,
                'ka'=> $request->georgian_title
            ],
            'slug'=>$slug,
            'user_id'=>auth()->user()->id,
            'release_date'=>$request->release_date,
            'description'=>[
                'en'=> $request->english_description,
                'ka'=> $request->georgian_description
            ],
            'director'=>[
                'en'=> $request->director_eng,
                'ka'=> $request->director_geo
            ],
            'income'=>$request->income,
            'thumbnail'=>'storage/movie-thumbnails/'.$file_name
        ]);
        $genres = Genre::whereIn('title->'.$request->lang, explode(",", $request->chosen_genres))->get();
        $movie->genres()->attach($genres);

        return response()->json(['message'=>'Movie added successfully.'], 200);
    }

    public function show(Request $request) : JsonResponse
    {
        $movie=Movie::where('slug', $request->slug)->with(['author','genres','quotes'=>['comments.author','author','movie','likes']])->first();
        if ($movie) {
            return response()->json($movie, 200);
        } else {
            return response()->json('error', 404);
        }
    }
    public function destroy(Movie $movie) : JsonResponse
    {
        $movie->delete();
        return response()->json(['message'=>'Movie deleted successfully.'], 200);
    }
    public function update(Request $request, Movie $movie) :JsonResponse
    {
        if ($request->img) {
            $file = $request->file('img');
            $file_name=time(). '.' . $file->getClientOriginalName();
            $file->move(public_path('storage/movie-thumbnails'), $file_name);
            $movie->update(['thumbnail'=>'storage/movie-thumbnails/'.$file_name]);
        }
        if ($request->chosen_genres) {
            $genres=Genre::whereIn('title->'.$request->lang, explode(",", $request->chosen_genres))->get();
            $movie->genres()->detach();
            $movie->genres()->attach($genres);
        }
        $slug = $this->slugify($request->english_title);
        $movie->update([
            'title'=>[
                'en'=> $request->english_title,
                'ka'=> $request->georgian_title
            ],
            'slug'=>$slug,
            'release_date'=>$request->release_date,
            'description'=>[
                'en'=> $request->english_description,
                'ka'=> $request->georgian_description
            ],
            'director'=>[
                'en'=> $request->director_eng,
                'ka'=> $request->director_geo
            ],
            'income'=>$request->income,
        ]);
        return response()->json('Movie updated successfully', 200);
    }
}
