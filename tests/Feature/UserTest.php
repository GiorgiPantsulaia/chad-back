<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Http\Resources\NotificationResource;
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
		])->assertStatus(500);
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
		$this->post(route('confirm.email'), ['email'=>'nonexistentemail@gmail.com'])->assertStatus(500);
	}

	public function test_user_can_reset_password()
	{
		User::create(['name'=>'test1234', 'email'=>'test@gmail.com', 'password'=>'test1234', 'google_user'=>false]);
		$user = User::first();
		$this->patch(route('reset.password'), ['code'=>$user->verification_code, 'password'=>'test1234', 'password_confirmation'=>'test1234'])->assertStatus(200);
	}

	public function test_user_reset_password_fails()
	{
		$this->patch(route('reset.password'), ['code'=>'invalid_token', 'password'=>'test1234', 'password_confirmation'=>'test1234'])->assertStatus(500);
	}

	public function test_user_can_comment_on_post()
	{
		$this->withoutMiddleware([Authenticate::class]);
		Quote::factory()->count(1)->create(['user_id'=>1]);
		$quote = Quote::first();
		User::factory()->count(1)->create();
		$user = User::find(2);
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
		$this->actingAs($user)->patch(route('mark.as-read', $notification->id))->assertOk();
	}

	public function test_user_can_mark_all_notifications_as_read()
	{
		$this->withoutMiddleware();
		User::factory()->count(1)->create();
		$user = User::first();
		Notification::factory()->count(2)->create(['recipient_id'=>$user->id]);
		$this->actingAs($user)->post(route('mark.all-read'))->assertOk();
	}

	public function test_user_can_update_their_information()
	{
		$this->withoutMiddleware([Authenticate::class]);
		User::factory()->count(1)->create(['name'=>'test123', 'email'=>'test@gmail.com']);
		$user = User::first();
		$this->actingAs($user)->patch(route('update.user', $user->id), [
			'name'                 => 'test456',
			'password'             => 'password1234',
			'password_confirmation'=> 'password1234',
			'img'                  => UploadedFile::fake()->create('test.png', $kilobytes = 0),
			'email'                => 'test1@gmail.com',
		])->assertOk();
	}

	public function test_user_can_update_email()
	{
		$this->withoutMiddleware([Authenticate::class]);
		User::factory()->count(1)->create(['name'=>'test123', 'email'=>'test@gmail.com']);
		$user = User::first();
		$this->actingAs($user)->patch(route('update.email'), [
			'verification_code'=> $user->verification_code,
			'email'            => 'test1@gmail.com',
		])->assertOk()->assertExactJson(['message'=> 'Email updated successfully']);
	}

	public function test_user_cannot_update_email_to_already_existing_one()
	{
		$this->withoutMiddleware([Authenticate::class]);
		User::factory()->count(1)->create(['name'=>'test123', 'email'=>'test@gmail.com']);
		User::factory()->count(1)->create(['name'=>'test1234', 'email'=>'test1@gmail.com']);
		$user = User::first();
		$this->actingAs($user)->patch(route('update.user', $user->id), [
			'email'=> 'test1@gmail.com',
		])->assertStatus(409);
	}

	public function test_notification_resource_returns_collection()
	{
		$comment = Notification::factory()->count(2)->create();
		$collect = NotificationResource::collection(Notification::all())->resolve();
		$this->assertCount(2, $collect);
	}
}
