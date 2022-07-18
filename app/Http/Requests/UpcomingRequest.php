<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpcomingRequest extends FormRequest
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
            'upcoming.title' => 'required',
            'upcoming.poster_path' => 'nullable|URL',
            'upcoming.backdrop_path' => 'nullable|URL',
            'upcoming.trailer_id' => 'required',
            'upcoming.genre' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'upcoming.title.required' => 'the name is required.',
            'upcoming.poster_path.u_r_l' => 'the poster_path must be a URL',
            'upcoming.backdrop_path.u_r_l' => 'the backdrop_path must be a URL',
            'upcoming.trailer_id.required' => 'the trailer id is required.',
             'upcoming.link.genre' => 'the genre is required.',
        ];
    }
}
