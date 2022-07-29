<?php

namespace App\Http\Requests\QuoteRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuoteRequest extends FormRequest
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
            'body'=>'required',
            'user_id'=>'required',
            'movie_id'=>'required',
            'thumbnail'=>'sometimes'
        ];
    }
    protected function prepareForValidation() :void
    {
        $this->merge([
        'body'=>[
            'en'=> $this->english_quote,
            'ka'=> $this->georgian_quote
        ],
        'user_id' => auth()->user()->id,
    ]);
    }
}
