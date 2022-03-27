<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferValidate extends FormRequest
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
            'to_phone' => 'required',
            'amount' => 'required|numeric|gt:1000',
            'hash_value' => 'required',
        ];
    }

    public function messages(){
        return [
            'to_phone.required' => 'Please Fill the Phone Number',
            'amount.required' => 'Please Fill Amount information',
            'amount.min'=>'The amount must be at least 1000 characters',
            'hash_value'=>"Hash Value also need",
        ];
    }
}
