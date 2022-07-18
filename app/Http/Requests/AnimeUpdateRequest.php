<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimeUpdateRequest extends FormRequest
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
            'anime.id' => 'exists:animes,id',
            'anime.name' => 'required',
            'anime.vote_average' => 'nullable|numeric',
            'anime.vote_count' => 'nullable|numeric',
            'anime.popularity' => 'nullable|numeric',
            'anime.release_date' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'anime.id' => 'the anime does not exist in the database.',
            'anime.id.integer' => 'the id must be an integer.',
            'anime.name.required' => 'the name is required.',
            'anime.vote_average.numeric' => 'the vote_average must be a number',
            'anime.vote_count.numeric' => 'the vote_count must be a number',
            'anime.popularity.numeric' => 'the popularity must be a number',
            'anime.release_date.numeric' => 'the release_date must be a date',
        ];
    }
}
