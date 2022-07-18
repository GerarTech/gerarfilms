<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdsRequest extends FormRequest
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
            'ads.title' => 'required',
            'ads.link' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'ads.title.required' => 'the title is required.',
            'ads.link.u_r_l' => 'the link is required',

        ];
    }
}
