<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieUpdateRequest extends FormRequest
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
            'movie.id' => 'exists:movies,id',
            'movie.title' => 'required',
            'movie.vote_average' => 'nullable|numeric',
            'movie.vote_count' => 'nullable|numeric',
            'movie.popularity' => 'nullable|numeric',
            'movie.subs' => 'nullable|URL',
            'movie.release_date' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'movie.id' => 'the movie does not exist in the database.',
            'movie.title.required' => 'the title is required.',
            'movie.preview_path.u_r_l' => 'the preview_path must be a URL',
            'movie.vote_average.numeric' => 'the vote_average must be a number',
            'movie.vote_count.numeric' => 'the vote_count must be a number',
            'movie.popularity.numeric' => 'the popularity must be a number',
            'movie.subs.u_r_l' => 'the subs must be a URL',
            'movie.release_date.numeric' => 'the release_date must be a date',
        ];
    }
}
