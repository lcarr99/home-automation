<?php

namespace App\Domain\Budgeting\Instructions;

use App\Models\User;

class DeletePaymentInstructions
{
    /**
     * @param User $user
     * @param int $paymentId
     */
    public function __construct(private User $user, private int $paymentId)
    {}

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getPaymentId(): int
    {
        return $this->paymentId;
    }
}
