<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index() :JsonResponse
    {
        return response()->json(Genre::all(), 200);
    }
}
