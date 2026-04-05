<?php

namespace App\Http\Validation;

class LoginValidation extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}