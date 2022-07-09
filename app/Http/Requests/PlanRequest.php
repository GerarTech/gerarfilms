<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
            'plan.name' => 'required',
            'plan.price' => 'required',
            'plan.pack_duration' => 'numeric',
    

        ];
    }

    public function messages()
    {
        return [
            'plan.name.required' => 'the name is required.',
            'plan.price.required' => 'the price is required',
            'plan.pack_duration.required' => 'the pack duration must be a number',

        ];
    }
}
