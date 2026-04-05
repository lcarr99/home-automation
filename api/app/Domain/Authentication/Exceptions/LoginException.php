<?php

namespace App\Domain\Authentication\Exceptions;

use Exception;

class LoginException extends Exception
{
    /**
     * @return static
     */
    public static function credentialsNotFound(): self
    {
        return new self('Credentials not found');
    }
}
