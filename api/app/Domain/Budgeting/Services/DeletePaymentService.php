<?php

namespace App\Domain\Budgeting\Services;

use App\Domain\Budgeting\Exceptions\PaymentException;
use App\Domain\Budgeting\Instructions\DeletePaymentInstructions;

class DeletePaymentService
{
    /**
     * @param DeletePaymentInstructions $instructions
     * @throws PaymentException
     */
    public function deletePayment(DeletePaymentInstructions $instructions): void
    {
        $payment = $instructions
            ->getUser()
            ->payments()
            ->where('id', '=', $instructions->getPaymentId())
            ->whereNull('deleted_at')
            ->first();

        if ($payment === null) {
            throw PaymentException::notFound($instructions->getPaymentId());
        }

        $payment->delete();
    }
}
