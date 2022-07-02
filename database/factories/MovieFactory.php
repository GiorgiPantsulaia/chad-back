<?php

namespace Database\Factories;

use App\Models\User;
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
            'user_id'=>User::factory(),
            'title'=>$this->faker->text(10),
            'slug'=>$this->faker->text(10),
			'thumbnail'=>$this->faker->sentence(),
            'release_date'=>$this->faker->date(),
            'genre'=>$this->faker->text(5),
            'description'=>$this->faker->sentence(),
            'director'=>$this->faker->text(10),
            'income'=>$this->faker->numberBetween(1000,20000),
        ];
    }
}
