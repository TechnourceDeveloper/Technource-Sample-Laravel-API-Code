<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ChangePasswordRequest extends FormRequest
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
            'old_password'=>'required|max:15',
            'new_password'=>'required|max:15|different:old_password',
            'confirm_password'=>'required|max:15|same:new_password'
        ];
    }
     public function failedValidation(Validator $validator) {
        $errors = $validator->errors()->first(); // Here is your array of errors

        $response = response()->json([
            'message' => $errors, 'response' => 201
                ], 201);
        throw new HttpResponseException($response);
    }
}
