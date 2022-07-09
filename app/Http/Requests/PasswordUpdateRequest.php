<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
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
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'the :attribute is required.',
            'password.confirmed' => 'the :attribute must be confirmed.',
            'password.min' => 'the :attribute must be a minimum of 6 characters.',
            'password_confirmation.required' => 'the :attribute  is required.',
            'password_confirmation.min' => 'the :attribute must be a minimum of 6 characters.',
        ];
    }
}
