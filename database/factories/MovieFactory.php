<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		return [
			'user_id'     => 21,
			'title'       => $this->faker->unique()->text(10),
			'slug'        => $this->faker->unique()->text(10),
			'thumbnail'   => null,
			'release_date'=> $this->faker->numberBetween(1990, 2023),
			'description' => $this->faker->sentence(),
			'director'    => $this->faker->text(10),
			'income'      => $this->faker->numberBetween(1000, 20000),
		];
	}
}
