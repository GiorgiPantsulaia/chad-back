<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    private function slugify(string $string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }
    public function index(): JsonResponse
    {
        $user = auth()->user();
        return response()->json(['data'=>Movie::latest()->where('user_id', $user->id)->with('quotes')->get()]);
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

        return response()->json(['message'=>'Movie added successfully.']);
    }

    public function show(Request $request) : JsonResponse
    {
        $movie=Movie::where('slug', $request->slug)->with('author')->with('genres')->with(['quotes'=>['comments.author','author','movie','likes']])->first();
        if ($movie) {
            return response()->json(['data'=>$movie]);
        } else {
            return response()->json('error', 404);
        }
    }
    public function destroy(Request $request) : JsonResponse
    {
        Movie::destroy($request->id);
        return response()->json(['message'=>'Movie deleted successfully.']);
    }
}
