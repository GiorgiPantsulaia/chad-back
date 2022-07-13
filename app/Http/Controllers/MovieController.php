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
    public function index(): JsonResponse
    {
        $user = auth()->user();
        return response()->json(['data'=>Movie::latest()->where('user_id', $user->id)->with('quotes')->get()]);
    }

    private function slugify($string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    public function create(Request $request): JsonResponse
    {
        $file = $request->file('img');
        $file_name=time(). '.' . $file->getClientOriginalName();
        $file->move(public_path('movie-thumbnails'), $file_name);
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
            'thumbnail'=>'movie-thumbnails/'.$file_name
        ]);
        $genres = Genre::where('title', $request->chosen_genres)->get();
        $movie->genres()->attach($genres);

        return response()->json(['message'=>'Movie added successfully.']);
    }

    public function show(Request $request) : JsonResponse
    {
        return response()->json(['data'=>Movie::where('slug', $request->slug)->with('genres')->with('quotes.comments')->first()]);
    }

    public function destroy(Request $request)
    {
        # code...
    }
}
