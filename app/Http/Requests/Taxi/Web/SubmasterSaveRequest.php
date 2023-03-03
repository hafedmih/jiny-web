<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;


class SubmasterSaveRequest extends FormRequest
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
            'name' => 'required|alpha',
            'amount' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|digits_between:1,15',
            'validity' => 'required|numeric',
        ];
    }
}
