<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
            'tmdb_api_key' => 'nullable|string',
            'purchase_key' => 'nullable|string',
            'tmdb_lang' => 'required|array',
            'app_name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'authorization.string' => ':attribute must be string',
            'tmdb_api_key.string' => ':attribute must be string',
            'purchase_key.string' => ':attribute must be string',
            'tmdb_lang.required' => ':attribute is required',
            'tmdb_lang.array' => ':attribute must be array',
            'app_name.string' => ':attribute must be string',

        ];
    }
}
