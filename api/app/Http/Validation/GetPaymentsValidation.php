<?php

namespace App\Http\Validation;

use Illuminate\Foundation\Http\FormRequest;

class GetPaymentsValidation extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'dateFrom' => ['sometimes', 'date_format:Y-m-d', 'before_or_equal:dateTo'],
            'dateTo' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:dateFrom'],
        ];
    }
}
