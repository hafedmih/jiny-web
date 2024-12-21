<?php

namespace App\Http\Requests\Taxi\API\Request;

use Illuminate\Foundation\Http\FormRequest;

class DriverEndRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_id'                  => 'required|exists:requests,id',
            'distance'                    => 'sometimes|required',
            'before_arrival_waiting_time' => 'sometimes|required',
            'after_arrival_waiting_time'  => 'sometimes|required',
            'drop_lat'                    => 'required',
            'drop_lng'                    => 'required',
            'drop_address'                => 'required'
        ];
    }
}
