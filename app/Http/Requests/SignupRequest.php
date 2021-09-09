<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\User;
use Illuminate\Validation\Rule;

class SignupRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $validation = [
            'email' => ['required','email','max:100',Rule::unique('users')->whereNull('deleted_at')],
            'username' => ['required','max:100',Rule::unique('users')->whereNull('deleted_at')],
            'name' => 'required|max:255',
            'date_of_birth' => 'required',
            //'user_type' => 'required',
            'account_type' => 'required',
            'device_type' => 'required',
            'device_token' => 'required'
        ];
    
        return $validation;
    }

    public function failedValidation(Validator $validator) {
        $errors = $validator->errors()->first(); // Here is your array of errors

        $response = response()->json(['message' => $errors, 'response' => 201], 201);
        throw new HttpResponseException($response);
    }

}
