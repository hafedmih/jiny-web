<?php

namespace App\Http\Requests\Taxi\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreSosRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone_number' => 'required',
            'title' => 'required',
            //'description' => 'required'
        ];
    }
}
