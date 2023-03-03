<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;

class FineRequest extends FormRequest
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
            // 'driver_name' => 'required',
            'fine_amount' => 'required',
            'description' => 'required',
            'date' => 'required',
        ];
    }
}