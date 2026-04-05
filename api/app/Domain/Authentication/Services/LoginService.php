<?php

namespace App\Domain\Authentication\Services;

use App\Domain\Authentication\Exceptions\LoginException;
use App\Domain\Authentication\Instructions\LoginInstructions;
use Illuminate\Auth\AuthManager;
use Illuminate\Session\SessionManager;

class LoginService
{
    /**
     * @param AuthManager $authManager
     * @param SessionManager $sessionManager
     */
    public function __construct(
        private AuthManager $authManager,
        private SessionManager $sessionManager
    )
    {}

    /**
     * @param LoginInstructions $instructions
     * @throws LoginException
     */
    public function login(LoginInstructions $instructions): void
    {
        if ($this->authManager->attempt([
            'email' => $instructions->getEmail(),
            'password' => $instructions->getPassword(),
        ]) === false) {
            throw LoginException::credentialsNotFound();
        }

        $this->sessionManager->regenerate();
    }
}
