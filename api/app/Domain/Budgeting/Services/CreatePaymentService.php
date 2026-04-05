<?php

namespace App\Domain\Budgeting\Services;

use App\Domain\Budgeting\Instructions\CreatePaymentInstructions;
use App\Models\Payment;

class CreatePaymentService
{
    /**
     * @param CreatePaymentInstructions $instructions
     * @return Payment
     */
    public function createPayment(CreatePaymentInstructions $instructions): Payment
    {
        $payment = new Payment();
        $payment->user_id = $instructions->getUser()->id;
        $payment->type = $instructions->getPaymentType();
        $payment->amount = $instructions->getAmount();
        $payment->description = $instructions->getDescription();

        $payment->save();

        return $payment;
    }
}
