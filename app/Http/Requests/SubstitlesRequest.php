<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubstitlesRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [

          //'substitle' => 'required|mimes:zip,application/x-subrip'
        ];
    }

    public function messages()
    {
        return [

            'substitle.required' => 'the link is required',
            'substitle.mimes' => 'Zip format required !'
        ];
    }
}
