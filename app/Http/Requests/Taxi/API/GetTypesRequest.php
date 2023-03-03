<?php

namespace App\Http\Requests\Taxi\API;

use Illuminate\Foundation\Http\FormRequest;

class GetTypesRequest extends FormRequest
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
            'pickup_lat' => 'required',
            'pickup_long' => 'required',
            'drop_lat' => 'required',
            'drop_long' => 'required',
            'pickup_address' => 'required',
            'drop_address' => 'required',
            'ride_type' => 'required', //value = 'RIDE_NOW' or 'RIDE_LATER'
            'ride_date' => 'required',
            'ride_time' => 'required'
        ];
    }
}
