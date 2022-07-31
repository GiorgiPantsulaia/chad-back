<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		return [
			'user_id'     => User::factory(),
			'recipient_id'=> User::factory(),
			'quote_id'    => Quote::factory(),
			'type'        => 'like',
			'state'       => 'unread',
		];
	}
}
