<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class DriverSaveRequest extends FormRequest
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
        //dd($data);
        if($res->has('slug') && $data['slug'] != "undefined"){
            
            $email = 'nullable|email|unique:users,email,'.$data['slug'].',slug,deleted_at,NULL';
            $phone = 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10';
            // $phone = 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10';
            
        }
        else{
            
            $email = 'nullable|email|unique:users,email';
            $phone = 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10';
                    
        }

        if($data['category'] == 'COMPANY' && $data['company'] == 1){
            $required = 'required';
        }
        else{
            $required = '';
        }

        return [
            'first_name' => 'required',
            // 'last_name' => 'required',
             'email' => $email,
            'phone_number' => $phone,
            'country' => 'required|exists:country,id',
            'type' => 'required|exists:vehicle,id',
            // 'gender' => 'required',
            'car_number' => 'required',
            'vehicle_model' => $data['vehicle_model'] != "" ? 'required' : '',
           // 'service_type' => 'required',
            'category' => 'required',
            'car_model' => $data['vehicle_model'] == "1" ? 'required' : '',
            'car_year' => 'required',
            'car_colour' => 'required',
            'company_name' => $required,
            'company_phone_number' => $required,            
            'total_no_of_vehicle' => $required,
        ];
    }
}
