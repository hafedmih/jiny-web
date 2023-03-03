<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class CategorySaveRequest extends FormRequest
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
        $category_id = [];
        $category_image = [];
        if($data['category_id'] != ""){
            $category_id = ['required'];
            $category_image = [];
        }
        else{
            $category_image = ['required'];
            $category_id = [];
        }
        return [
            'category_name' =>  ['required','regex:/^[\w-]*$/','max:55'],
            'category_image' => $category_image,
            'category_id' => $category_id
        ];
    }
}
