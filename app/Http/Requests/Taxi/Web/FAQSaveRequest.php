<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class FAQSaveRequest extends FormRequest
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
        $faq_id = [];
        if($data['faq_id'] != ""){
            $faq_id = ['required'];
        }
        else{
            $faq_id = [];
        }
        return [
            'question' => ['required'],
            'answer' => ['required'],
            'category' => ['required'],
            'language' => ['required'],
            'faq_id' => $faq_id
        ];
    }
}
