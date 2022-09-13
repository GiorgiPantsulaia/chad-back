<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
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
	Route::post('/register', 'create')->name('signup');
	Route::post('/login', 'login')->name('signin');
	Route::post('/logout', 'logout')->name('logout');
	Route::post('/confirm-email', 'confirmEmail')->name('confirm.email');
	Route::post('/verify-email', 'verifyEmail')->name('email.verification');
	Route::patch('/reset-password', 'resetPassword')->name('reset.password');
});

Route::middleware(['auth:api'])->group(function () {
	Route::post('/comments', [CommentController::class, 'create'])->name('add.comment');
	Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('delete.comment');
	Route::get('/genres', [GenreController::class, 'index'])->name('genres');
	Route::post('/genre-movies', [GenreController::class, 'show']);
	Route::post('/search', [SearchController::class, 'index'])->name('search');

	Route::controller(QuoteController::class)->group(function () {
		Route::get('/quotes/{quote}', 'show')->name('get.quote');
		Route::get('/all-quotes', 'index')->name('quotes');
		Route::get('/{user}/liked-posts', 'likedPosts')->name('liked.posts');
		Route::post('/like/{quote}', 'likePost')->name('add.like');
		Route::post('/unlike/{quote}', 'unlikePost')->name('remove.like');
		Route::post('/quotes', 'create')->name('post.quote');
		Route::patch('/update-quote/{quote}', 'update')->name('update.quote');
		Route::delete('/quote/{quote}', 'destroy')->name('delete.quote');
	});

	Route::controller(MovieController::class)->group(function () {
		Route::get('/user-movies', 'index')->name('user.movies');
		Route::post('/movies', 'create')->name('post.movie');
		Route::post('/movie-description', 'show')->name('movie.description');
		Route::patch('/edit-movie/{movie}', 'update')->name('update.movie');
		Route::delete('/movie/{movie}', 'destroy')->name('delete.movie');
	});

	Route::controller(NotificationController::class)->group(function () {
		Route::get('/notifications', 'index')->name('notifications');
		Route::post('/notifications-read', 'markAllRead')->name('mark.all-read');
		Route::patch('/notification-read/{notification}', 'markAsRead')->name('mark.as-read');
	});

	Route::controller(UserController::class)->group(function () {
		Route::get('/users/{user}', 'show')->name('get.user_data');
		Route::patch('/user/{user}', 'update')->name('update.user');
		Route::patch('/update-email', 'updateEmail')->name('update.email');
	});
	Route::controller(FriendController::class)->group(function () {
		Route::post('/friends/{user}', 'store')->name('send.friend-request');
		Route::post('/friends/{user}/accept', 'acceptFriend')->name('accept.friend_request');
		Route::post('/friends/{user}/deny', 'denyFriend')->name('deny.friend_request');
		Route::post('/unfriend/{user}', 'unfriend')->name('remove.friend');
		Route::get('/friends', 'index')->name('friends');
	});
});

Route::post('/auth-redirect', [OAuthController::class, 'redirect'])->name('redirect');
Route::get('/auth-callback', [OAuthController::class, 'callback'])->name('callback');
