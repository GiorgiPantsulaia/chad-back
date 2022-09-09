<?php

namespace App\Http\Requests\NotificationRequests;

use Illuminate\Foundation\Http\FormRequest;

class FriendNotificationRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'created_at'=> 'nullable',
			'user_id'   => 'required',
			'type'      => 'required',
			'state'     => 'required',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'created_at'=> now(),
			'user_id'   => auth()->user()->id,
			'type'      => 'friends',
			'state'     => 'unread',
			'status'    => 'pending',
		]);
	}
}
