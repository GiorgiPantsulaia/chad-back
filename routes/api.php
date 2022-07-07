<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::controller(AuthController::class)->group(function () {
    Route::post('/register-user', 'create')->name('signup');
    Route::post('/login', 'login')->name('signin');
    Route::post('/logout', 'logout')->name('logout');
});
Route::controller(QuoteController::class)->group(function () {
    Route::get('/all-quotes', 'index')->name('all.quotes');
    Route::post('/like-post', 'likePost')->name('addLike');
    Route::post('/unlike-post', 'unlikePost')->name('removeLike');
    Route::post('/post-quote', 'create')->name('post.quote');
});

Route::get('/all-movies', [MovieController::class, 'index'])->name('all.movies');

Route::post('/add-comment', [CommentController::class,'addComment'])->name('add.comment');

Route::post('/logged-user', [UserController::class,'index'])->name('logged.user');

Route::post('/auth-redirect', [OAuthController::class,'redirect'])->name('redirect');
Route::get('/auth-callback', [OAuthController::class,'callback'])->name('callback');
