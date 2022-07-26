<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SearchController;
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
    Route::post('/confirm-email', 'confirmEmail')->name('confirm-email');
    Route::post('/verify-email', 'verifyEmail')->name('email-verification');
    Route::patch('/reset-password', 'resetPassword')->name('reset-password');
});

Route::controller(QuoteController::class)->group(function () {
    Route::post('/get-quote', 'show')->name('get-quote');
    Route::post('/all-quotes', 'index')->name('all-quotes');
    Route::post('/like-post', 'likePost')->name('addLike');
    Route::post('/unlike-post', 'unlikePost')->name('removeLike');
    Route::post('/post-quote', 'create')->name('post-quote');
    Route::patch('/update-quote', 'update')->name('update-quote');
    Route::delete('/delete-quote', 'destroy')->name('delete-quote');
});

Route::controller(MovieController::class)->group(function () {
    Route::get('/user-movies', 'index')->name('user-movies');
    Route::post('/post-movie', 'create')->name('post-movie');
    Route::post('/movie-description', 'show')->name('movie-description');
    Route::patch('/update-movie', 'update')->name('update-movie');
    Route::delete('/delete-movie', 'destroy')->name('delete-movie');
});

Route::controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index')->name('notifications');
    Route::get('/notifications-read', 'markAllRead')->name('mark-all-read');
    Route::post('/notification-read', 'markAsRead')->name('mark-as-read');
});

Route::controller(UserController::class)->group(function () {
    Route::post('/update-user', 'update')->name('update-user');
    Route::post('/update-email', 'updateEmail')->name('update-email');
    Route::post('/logged-user', 'index')->name('logged-user');
});

Route::controller(OAuthController::class)->group(function () {
    Route::get('/auth-callback', 'callback')->name('callback');
    Route::post('/auth-redirect', 'redirect')->name('redirect');
});
Route::post('/add-comment', [CommentController::class,'addComment'])->name('add-comment');
Route::get('/genres', [GenreController::class,'index'])->name('all-genres');
Route::post('/search', [SearchController::class,'index'])->name('search');
