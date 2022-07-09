<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LivetvRequest extends FormRequest
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
            'livetv.name' => 'required',
            'livetv.poster_path' => 'nullable|URL',
            'livetv.backdrop_path' => 'nullable|URL',
            'livetv.link' => 'nullable|URL',
        ];
    }

    public function messages()
    {
        return [
            'livetv.name.required' => 'the name is required.',
            'livetv.poster_path.u_r_l' => 'the poster_path must be a URL',
            'livetv.backdrop_path.u_r_l' => 'the backdrop_path must be a URL',
        ];
    }
}
