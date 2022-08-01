<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GenreSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$json = File::get('database/data/genres.json');
		$genres = json_decode($json);

		foreach ($genres as $genre)
		{
			Genre::create([
				'title' => [
					'en'=> $genre->en,
					'ka'=> $genre->ka,
				],
			]);
		}
	}
}
