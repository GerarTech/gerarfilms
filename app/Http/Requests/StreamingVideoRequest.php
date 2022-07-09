<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StreamingVideoRequest extends FormRequest
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
            'video' => 'required|mimes:m3u8,ts,dash'
        ];
    }
    public function messages()
    {
        return [
            'video.required' => 'the video is required',
            'video.mimes' => 'The video needs a m3u8 , ts , Dash' 
        ];
    }
}
