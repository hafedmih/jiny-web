<?php

namespace App\Http\Requests\Taxi\API;

use Illuminate\Foundation\Http\FormRequest;

class CancellationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_id'    => 'required|exists:requests,id',
            'reason'        => 'sometimes|required|exists:cancellation_reasons,id',
            'custom_reason' => 'sometimes|required|min:5|max:100',
            'user_lat' => 'sometimes|required',
            'user_lng' => 'sometimes|required',
            'driver_lat' => 'sometimes|required',
            'driver_lng' => 'sometimes|required',
            'user_location' => 'sometimes|required',
            'driver_location' => 'sometimes|required'
        ];
    }
}
