<?php

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
			'name'    => 'sometimes',
			'email'   => 'sometimes',
			'password'=> 'required',
		];
	}

	protected function prepareForValidation(): void
	{
		$login = $this->name;
		$nameOrEmail = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
		$this->merge([$nameOrEmail => $login,
		]);
	}
}
