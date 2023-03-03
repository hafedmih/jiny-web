<?php

namespace App\Http\Requests\Taxi\API\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class CreateTripRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(res $res)
    {
        return [
            'pick_lat' => 'required',
            'pick_lng' => 'required',
            'pick_address' => 'required',
            // 'drop_lat' => 'required',
            // 'drop_lng' => 'required',
            // 'drop_address' => 'required',
            'vehicle_type' => $res->ride_type == "RENTAL" ? '' : 'required',
            'payment_opt' => 'required',
            'ride_type' => 'required'
        ];
    }
}
