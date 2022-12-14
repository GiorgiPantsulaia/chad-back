<?php

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
			'name'              => 'required|max:15|min:3|regex:/^[a-z0-9 ]*$/',
			'email'             => 'required|email|max:255|unique:users,email',
			'password'          => 'required|confirmed|min:8|max:15|regex:/^[a-z0-9 ]*$/',
			'google_user'       => 'nullable',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'google_user'      => false,
		]);
	}
}
