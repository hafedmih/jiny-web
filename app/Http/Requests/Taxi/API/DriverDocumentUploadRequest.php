<?php

namespace App\Http\Requests\Taxi\API;

use Illuminate\Foundation\Http\FormRequest;

class DriverDocumentUploadRequest extends FormRequest
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
            'document_id' => 'required',
            'document_image' => 'required'
        ];
    }
}
