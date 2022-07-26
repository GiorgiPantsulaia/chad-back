<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'img'=>'sometimes|image',
            'name'=>'nullable|min:3|max:15|regex:/^[a-z0-9]*$/',
            'email'=>'sometimes|email',
            'password'=>'nullable|confirmed|min:3|max:15'
        ];
    }
}