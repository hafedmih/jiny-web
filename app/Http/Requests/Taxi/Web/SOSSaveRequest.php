<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class SOSSaveRequest extends FormRequest
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
    public function rules(res $res)
    {
        $data = $res->all();
        $sos_id = [];
        if($data['sos_id'] != ""){
            $sos_id = ['required'];
        }
        else{
            $sos_id = [];
        }
        return [
            'phone_number' => ['required'],
            'title' => ['required'],
            'language' => ['required'],
            'sos_id' => $sos_id
        ];
    }
}
