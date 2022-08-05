<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class QuoteTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();
		$this->withoutMiddleware([Authenticate::class]);
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_user_can_get_all_quotes()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(10)->create();
		$this->actingAs($user)->get(route('quotes'))->assertOk();
	}

	public function test_user_can_like_quote()
	{
		User::factory()->count(1)->create();
		Quote::factory()->count(1)->create();
		$user = User::first();
		$quote = Quote::first();
		$this->actingAs($user)->post(route('add.like', $quote->id))->assertOk();
	}

	public function test_user_can_unlike_quote()
	{
		User::factory()->count(1)->create();
		Quote::factory()->count(1)->create();
		$user = User::first();
		$quote = Quote::first();
		$this->actingAs($user)->post(route('add.like', $quote->id));
		$this->actingAs($user)->post(route('remove.like', $quote->id))->assertOk();
	}

	public function test_user_can_add_quote()
	{
		User::factory()->count(1)->create();
		Movie::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->post(route('post.quote'), [
			'english_quote'     => 'body',
			'georgian_quote'    => 'ციტატა',
			'movie_id'          => Movie::first()->id,
			'img'               => UploadedFile::fake()->create('test.png', $kilobytes = 0),
		])->assertStatus(201);
	}

	public function test_quote_image_required()
	{
		User::factory()->count(1)->create();
		Movie::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->post(route('post.quote'), [
			'english_quote'     => 'body',
			'georgian_quote'    => 'ციტატა',
			'movie_id'          => Movie::first()->id,
		])->assertStatus(422);
	}

	public function test_user_can_delete_quote()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->actingAs($user)->delete(route('delete.quote', $quote->id))->assertOk();
	}

	public function test_user_can_edit_quote()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->actingAs($user)->patch(route('update.quote', $quote->id), [
			'english_quote'     => 'body',
			'georgian_quote'    => 'ციტატა',
			'movie_id'          => $quote->movie_id,
			'img'               => UploadedFile::fake()->create('test.png', $kilobytes = 0),
		])->assertOk();
	}

	public function test_user_can_get_single_quote()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->actingAs($user)->get(route('get.quote', $quote->id))->assertOk();
	}

	public function test_comment_resource_returns_collection()
	{
		$comment = Comment::factory()->count(2)->create();
		$collect = CommentResource::collection(Comment::all())->resolve();
		$this->assertCount(2, $collect);
	}
}
