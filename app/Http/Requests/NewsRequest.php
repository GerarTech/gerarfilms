<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
            'article.title' => 'required',
            'article.poster_path' => 'nullable|URL',
            'article.overview' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'article.title.required' => 'the name is required.',
            'article.poster_path.u_r_l' => 'the poster_path must be a URL',
            'article.overview.required' => 'the overview is required',

        ];
    }
}
