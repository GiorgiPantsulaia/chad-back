<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		$fakerKa = \Faker\Factory::create('ka_GE');
		return [
			'title'=> [
				'en'=> $this->faker->text(15),
				'ka'=> $fakerKa->text(15),
			],
		];
	}
}
