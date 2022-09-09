<?php

namespace App\Http\Requests\NotificationRequests;

use Illuminate\Foundation\Http\FormRequest;

class CommentNotificationRequest extends FormRequest
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
			'created_at'  => 'nullable',
			'user_id'     => 'required',
			'type'        => 'required',
			'state'       => 'required',
			'recipient_id'=> 'required',
			'quote_id'    => 'required',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'created_at'=> now(),
			'user_id'   => auth()->user()->id,
			'type'      => 'comment',
			'state'     => 'unread',
			'status'    => null,
		]);
	}
}
