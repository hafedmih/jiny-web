<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        if(array_key_exists('user_id', $data) && $data['user_id'] != ''){
            return [
                'first_name' => ['required','max:55'],
                'last_name' => ['required','max:55'],
                'email' => ['required', 'string', 'email','unique:users,email,'.$data['user_id'].',slug,deleted_at,NULL'],
                'phone_number' => ['required','regex:/^([0-9\s\-\+\(\)]*)$/','min:10'],
            ];
        }
        elseif(array_key_exists('user_slug', $data) && $data['user_slug'] != ''){
            return [
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'cpassword' => ['required','same:password']
            ];
        }
        else{
            return [
                'first_name' => ['required','max:55'],
                'last_name' => ['required','max:55'],
                'company_name' => ['required','max:55'],
                'company_code' => ['required','max:55'],
                'language' => ['required'],
                'no_of_vehicles' => ['required'],
                'email' => ['required', 'string', 'email','unique:users'],
                'role' => ['required', 'string'],
                'phone_number' => ['required','regex:/^([0-9\s\-\+\(\)]*)$/','min:10'],
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'cpassword' => ['required','same:password']
            ];
        }
    }
}
