<?php

namespace App\Http\Validation;

use App\Domain\Budgeting\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreatePaymentValidation extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(PaymentType::class)],
            'amount' => ['required', 'decimal:2', 'max:9999999.99', 'gt:0.00'],
            'description' => ['required', 'string', 'max:100'],
        ];
    }
}
