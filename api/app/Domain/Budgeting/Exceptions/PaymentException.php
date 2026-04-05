<?php

namespace App\Domain\Budgeting\Exceptions;

use Exception;

class PaymentException extends Exception
{
    /**
     * @param int $id
     * @return $this
     */
    public static function notFound(int $id): self
    {
        return new self(sprintf('Payment not found by %s', $id));
    }
}
