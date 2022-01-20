<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class LoginRequest extends FormRequest
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

        $validation = [
            'device_type' => 'required',
            'device_token' => 'required',
            'account_type' => 'required'
        ];
        if ($this->account_type == User::NORMAL_SIGNUP) {
            $validation['password'] = 'required';
            $validation['username'] = 'required';
        } else {
            $validation['account_type_id'] = 'required';
        }
        return $validation;
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->first(); // Here is your array of errors

        $response = response()->json([
            'message' => $errors, 'response' => 201
        ], 201);
        throw new HttpResponseException($response);
    }
}
