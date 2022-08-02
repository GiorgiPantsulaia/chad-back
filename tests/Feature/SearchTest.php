<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_user_can_search_movies()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		Movie::factory()->count(1)->create(['title'=>['en'=>'Movie', 'ka'=>'ფილმი']]);
		$user = User::first();
		$this->actingAs($user)->post(route('search'), ['search'=>'@Movie'])
		->assertSee('movies');
	}

	public function test_user_can_search_quotes()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		Quote::factory()->count(1)->create(['body'=>['en'=>'quote', 'ka'=>'ციტატა']]);
		$user = User::first();
		$this->actingAs($user)->post(route('search'), ['search'=>'#quote'])
	->assertSee('quotes');
	}

	public function test_search_not_found()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->post(route('search'), ['search'=>'quote without #'])
		->assertStatus(422);
	}
}
