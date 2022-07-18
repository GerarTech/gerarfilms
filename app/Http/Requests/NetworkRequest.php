<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NetworkRequest extends FormRequest
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
            'name' => 'required|unique:networks,name,' . $this->request->get('id')
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'the network name is required.',
            'name.unique' => 'the network already exists.',
        ];
    }
}
