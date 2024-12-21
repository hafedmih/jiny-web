<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class ComplaintSaveRequest extends FormRequest
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
        $slug = [];
        if($data['slug'] != ""){
            $slug = ['required'];
        }
        else{
            $slug = [];
        }
        return [
            'title' => ['required'],
            // 'category' => ['required'],
            'type' => ['required'],
            'slug' => $slug
        ];
    }
}
