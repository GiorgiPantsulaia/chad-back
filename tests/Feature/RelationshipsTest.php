<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipsTest extends TestCase
{
	use RefreshDatabase;

	public function test_user_has_movies()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		Movie::factory()->count(1)->create(['user_id'=>1]);
		$this->assertTrue($user->movies != null);
	}

	public function test_user_has_notifications()
	{
		User::factory()->count(2)->create();
		$user = User::first();
		Notification::factory()->count(1)->create();
		$this->assertTrue($user->notifications != null);
	}

	public function test_user_has_liked_posts()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$user->likedPosts()->attach($quote);
		$this->assertTrue($user->likedPosts->count() > 0);
	}

	public function test_quote_has_comments()
	{
		Comment::factory()->count(1)->create();
		$quote = Quote::first();
		$this->assertTrue($quote->comments->count() > 0);
	}

	public function test_quote_has_movie()
	{
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->assertTrue($quote->movie !== null);
	}

	public function test_quote_has_author()
	{
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->assertTrue($quote->author !== null);
	}

	public function test_quote_has_likes()
	{
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$quote->likes()->attach(User::first());
		$this->assertTrue($quote->likes->count() > 0);
	}

	public function test_notification_has_sender()
	{
		Notification::factory()->count(1)->create();
		$notification = Notification::first();
		$this->assertTrue($notification->sender !== null);
	}

	public function test_notification_has_recipient()
	{
		Notification::factory()->count(1)->create();
		$notification = Notification::first();
		$this->assertTrue($notification->recipient !== null);
	}

	public function test_notification_has_quote()
	{
		Notification::factory()->count(1)->create();
		$notification = Notification::first();
		$this->assertTrue($notification->quote !== null);
	}

	public function test_movie_has_author()
	{
		Movie::factory()->count(1)->create();
		$movie = Movie::first();
		$this->assertTrue($movie->author !== null);
	}

	public function test_movie_has_quotes()
	{
		Quote::factory()->count(1)->create();
		$movie = Movie::first();
		$this->assertTrue($movie->quotes !== null);
	}

	public function test_movie_has_genres()
	{
		Movie::factory()->count(1)->create();
		Genre::factory()->count(1)->create();
		$movie = Movie::first();
		$movie->genres()->attach(Genre::first());
		$this->assertTrue($movie->genres !== null);
	}

	public function test_comment_has_quote()
	{
		Comment::factory()->count(1)->create();
		$comment = Comment::first();
		$this->assertTrue($comment->quote !== null);
	}

	public function test_comment_has_author()
	{
		Comment::factory()->count(1)->create();
		$comment = Comment::first();
		$this->assertTrue($comment->author !== null);
	}

	public function test_genre_has_movies()
	{
		Genre::factory()->count(1)->create();
		$genre = Genre::first();
		Movie::factory()->count(1)->create();
		$movie = Movie::first();
		$movie->genres()->attach(Genre::first());
		$this->assertTrue($genre->movies !== null);
	}
}
