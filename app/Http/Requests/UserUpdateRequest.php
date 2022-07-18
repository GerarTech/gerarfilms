<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'user.name' => 'required',
            'user.email' => 'required|email:rfc,dns',
        ];
    }

    public function messages()
    {
        return [
            'user.name.required' => 'the :attribute is required.',
            'user.email.required' => 'the :attribute is required.',
        ];
    }
}
