<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
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
            'image' => 'required|dimensions:min_width=200|mimes:jpeg,jpg,png',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'the image is required',
            'image.dimensions' => 'the image need min width 300px',
            'image.mimes' => 'The image needs a JPG, JPEG or PNG format'
        ];
    }
}
