<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;

class CancellationReasonSaveRequest extends FormRequest
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
            'cancellation_reason' => ['required'],
            'reason_type' => ['required'],
            'trip_status' => ['required'],
            'pay_status' => ['required'],
        ];
    }
}
