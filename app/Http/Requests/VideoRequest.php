<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
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
            'video' => 'required|mimes:mp4,webm,ogv,flv,mkv,m3u8,ts'
        ];
    }
    public function messages()
    {
        return [
            'video.required' => 'the video is required',
            'video.mimes' => 'The video needs a MP4, WEBM, OGV or FLV or MKV format' 
        ];
    }
}
