<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewRequest extends FormRequest
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
            'preview.title' => 'required',
            'preview.backdrop_path' => 'nullable|URL',
            'preview.minicover' => 'nullable|URL',
            'preview.link' => 'required',
    
        ];
    }

    public function messages()
    {
        return [
            'preview.title.required' => 'the name is required.',
            'preview.backdrop_path.u_r_l' => 'the backdrop path must be a URL',
            'preview.minicover.u_r_l' => 'the minicover path must be a URL',
            'preview.link.required' => 'the trailer id is required.',

        ];
    }
}
