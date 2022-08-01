<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_user_redirects_to_google_auth()
	{
		User::factory()->count(1)->create();
		$this->post(route('redirect'))->assertOk();
	}

	// public function test_user_can_register_with_google()
	// {
	// 	$this->get(route('callback'))->assertRedirect();
	// }
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
}
