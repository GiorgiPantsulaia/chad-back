<?php

namespace App\Http\Requests\MovieRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'title'       => 'sometimes',
			'description' => 'sometimes',
			'director'    => 'sometimes',
			'release_date'=> 'sometimes',
			'income'      => 'sometimes',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'title'=> [
				'en'=> $this->english_title,
				'ka'=> $this->georgian_title,
			],
			'description'=> [
				'en'=> $this->english_description,
				'ka'=> $this->georgian_description,
			],
			'director'=> [
				'en'=> $this->director_eng,
				'ka'=> $this->director_geo,
			],
		]);
	}
}
