<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MovieTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_authenticated_user_can_see_genres_while_adding_movie()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->get(route('genres'))->assertOk();
	}

	public function test_user_can_see_their_movies()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->get(route('user.movies'))->assertOk();
	}

	public function test_user_can_add_movie()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->post(route('post.movie'), [
			'english_title'    => 'title',
			'georgian_title'   => 'სახელი',
			'user_id'          => $user->id,
			'description'      => ['en'=>'description', 'ka'=>'აღწერა'],
			'director'         => ['en'=>'director', 'ka'=>'რეჟისორი'],
			'release_date'     => 2000,
			'income'           => 222222,
			'img'              => UploadedFile::fake()->create('test.png', $kilobytes = 0),
		])->assertStatus(201);
	}

	public function test_user_can_see_movie_description()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Movie::factory()->count(1)->create(['slug'=>'slug']);
		$movie = Movie::first();
		$this->actingAs($user)->post(route('movie.description'), [
			'slug'=> $movie->slug,
		])->assertOk();
	}

	public function test_user_can_delete_movie()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Movie::factory()->count(1)->create(['user_id'=>$user->id]);
		$movie = Movie::first();
		$this->actingAs($user)->delete(route('delete.movie', $movie->id))->assertOk();
	}

	public function test_user_can_edit_movie()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Movie::factory()->count(1)->create(['user_id'=>$user->id]);
		$movie = Movie::first();
		$this->actingAs($user)->patch(route('update.movie', $movie->id), [
			'english_title'    => 'title',
			'georgian_title'   => 'სახელი',
			'description'      => ['en'=>'description', 'ka'=>'აღწერა'],
			'director'         => ['en'=>'director', 'ka'=>'რეჟისორი'],
			'release_date'     => 2000,
			'income'           => 222222,
			'img'              => UploadedFile::fake()->create('test.png', $kilobytes = 0),
			'chosen_genres'    => 'Adventure',
		])->assertOk();
	}
}
