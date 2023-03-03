<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class VehicleSaveRequest extends FormRequest
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
        $images = [];
        $highlightimages = [];
        if($data['vehicle_id'] != ""){
            $sos_id = ['required'];
            $images = [];
            $highlightimages = [];

        }
        else{
            $sos_id = [];
            $images = ['required'];
            $highlightimages = ['required'];

        }
        return [
            // 'vehicle_name' => ['required','regex:/^[a-zA-Z0-9]+$/u','max:255'],
            'vehicle_name' => ['required','max:255'],
            'image' => $images,
            'highlight_image' => $highlightimages,
            'capacity' => ['required', 'string', 'max:255'],
            'category_id' => ['required'],
            
        ];
    }
}
