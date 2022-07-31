<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_user_can_register()
	{
		$this->post(route('signup'), [
			'name'                  => 'test1234',
			'email'                 => 'test@gmail.com',
			'password'              => 'test1234',
			'password_confirmation' => 'test1234',
		])->assertCreated();
	}

	public function test_user_can_login()
	{
		User::create(['name'=>'test1234', 'email'=>'test@gmail.com', 'password'=>'test1234', 'google_user'=>false]);
		$user = User::first();
		$user->email_verified_at = now();
		$user->save();
		$this->post(route('signin'), [
			'name'    => 'test1234',
			'password'=> 'test1234',
		])->assertOk();
	}

	public function test_user_can_toggle_remember_me()
	{
		User::create(['name'=>'test1234', 'email'=>'test@gmail.com', 'password'=>'test1234', 'google_user'=>false]);
		$user = User::first();
		$user->email_verified_at = now();
		$user->save();
		$this->post(route('signin'), [
			'name'       => 'test1234',
			'password'   => 'test1234',
			'remember_me'=> true,
		])->assertOk()->assertJsonFragment(['expires_in'=>432000]);
	}

	public function test_user_login_fails_if_email_not_verified()
	{
		User::create(['name'=>'test1234', 'email'=>'test@gmail.com', 'password'=>'test1234', 'google_user'=>false]);
		$user = User::first();
		$this->post(route('signin'), [
			'name'    => $user->name,
			'password'=> 'test1234',
		])->assertStatus(403);
	}

	public function test_user_login_fails_if_incorrect_credentials()
	{
		User::factory()->count(1)->create();
		$this->post(route('signin'), [
			'name'    => 'nonexistentname',
			'password'=> 'incorrectpassword',
		])->assertStatus(401);
	}

	public function test_user_can_logout()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user);
		$this->post(route('logout'))->assertOk();
	}

	public function test_user_can_verify_email()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user);
		$this->post(route('email.verification'), [
			'token'=> $user->verification_code,
		])->assertOk();
	}

	public function test_user_verify_email_fails()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user);
		$this->post(route('email.verification'), [
			'token'=> 'invalid_token',
		])->assertStatus(400);
	}

	public function test_user_can_confirm_email()
	{
		User::factory()->count(1)->create();
		$user = User::first();
		$this->post(route('confirm.email'), ['email'=>$user->email])->assertOk();
	}

	public function test_user_confirm_email_failed()
	{
		User::factory()->count(1)->create();
		$this->post(route('confirm.email'), ['email'=>'nonexistentemail@gmail.com'])->assertStatus(404);
	}

	public function test_user_can_reset_password()
	{
		User::create(['name'=>'test1234', 'email'=>'test@gmail.com', 'password'=>'test1234', 'google_user'=>false]);
		$user = User::first();
		$this->patch(route('reset.password'), ['code'=>$user->verification_code, 'password'=>'test1234', 'password_confirmation'=>'test1234'])->assertStatus(200);
	}

	public function test_user_reset_password_fails()
	{
		$this->patch(route('reset.password'), ['code'=>'invalid_token', 'password'=>'test1234', 'password_confirmation'=>'test1234'])->assertStatus(404);
	}

	public function test_user_can_comment_on_post()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create(['id'=>1]);
		Quote::factory()->count(1)->create(['user_id'=>1]);
		$quote = Quote::first();
		User::factory()->count(1)->create(['id'=>3]);
		$user = User::find(3);
		$this->actingAs($user)->post(
			route('add.comment'),
			[
				'user_id'     => $user->id,
				'quote_id'    => $quote->id,
				'recipient_id'=> 1,
				'body'        => 'new comment',
			]
		)->assertOk();
	}

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

	public function test_user_cannot_get_movie_description_if_incorrect_slug()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Movie::factory()->count(1)->create();
		$this->actingAs($user)->post(route('movie.description'), [
			'slug'=> 'incorrect-slug',
		])->assertStatus(404);
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

	public function test_user_can_get_notifications()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		$this->actingAs($user)->get(route('notifications'))->assertOk();
	}

	public function test_user_can_mark_notification_as_read()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Notification::factory()->count(1)->create(['recipient_id'=>$user->id]);
		$notification = Notification::first();
		$this->actingAs($user)->patch(route('mark.as-read'), ['id'=> $notification->id])->assertOk();
	}

	public function test_user_can_mark_all_notifications_as_read()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Notification::factory()->count(2)->create(['recipient_id'=>$user->id]);
		$this->actingAs($user)->post(route('mark.all-read'))->assertOk();
	}

	public function test_user_redirects_to_google_auth()
	{
		User::factory()->count(1)->create();
		$this->post(route('redirect'))->assertOk();
	}

	// public function test_user_can_register_with_google()
	// {
	// 	$this->get(route('callback'))->assertRedirect();
	// }

	public function test_user_can_get_all_quotes()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(10)->create();
		$this->actingAs($user)->get(route('quotes'))->assertOk();
	}

	// public function test_user_can_like_quote()
	// {
	// 	$this->withoutMiddleware();
	// 	Quote::factory()->count(1)->create();
	// 	User::factory()->count(1)->create();
	// 	$user = User::first();
	// 	$quote = Quote::first();
	// 	$this->actingAs($user)->post(route('add.like', $quote->id))->assertOk();
	// }
	public function test_user_can_add_quote()
	{
		$this->withoutMiddleware();
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

	public function test_user_can_delete_quote()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->actingAs($user)->delete(route('delete.quote', $quote->id))->assertOk();
	}

	public function test_user_can_edit_quote()
	{
		$this->withoutMiddleware();
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
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Quote::factory()->count(1)->create();
		$quote = Quote::first();
		$this->actingAs($user)->get(route('get.quote', $quote->id))->assertOk();
	}

	public function test_user_can_update_their_information()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create(['name'=>'test123', 'email'=>'test@gmail.com']);
		$user = User::first();
		$this->actingAs($user)->patch(route('update.user', $user->id), [
			'password'             => 'password1234',
			'password_confirmation'=> 'password1234',
		])->assertOk();
	}
}
