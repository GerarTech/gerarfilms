<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeaturedRequest extends FormRequest
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
            'featured.title' => 'required',
            'featured.poster_path' => 'required|URL',
            'featured.type' => 'required',
            'featured.genre' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'featured.title.required' => 'the name is required.',
            'featured.poster_path.u_r_l' => 'the poster_path must be a URL',
            'featured.type' => 'the type is required.',
        ];
    }
}
