<?php

namespace App\Http\Requests\Taxi\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as res;

class DocumentUploadRequest extends FormRequest
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
        $required = '';
        if($res->date_required == '1' || $res->date_required == '2'  || $res->has('date_required') == null){
            $required = 'required';
        }
        return [
            'document_image' => 'required|file|mimes:jpeg,jpg,png,gif',
            'expiry_date' => $required
        ];
    }
}
