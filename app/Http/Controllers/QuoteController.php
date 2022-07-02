<?php

namespace App\Http\Controllers;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        return Quote::with('movie')->with('author')->paginate(5);
    }
}
