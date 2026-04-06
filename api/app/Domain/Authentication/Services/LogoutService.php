<?php

namespace App\Domain\Authentication\Services;

use Illuminate\Auth\AuthManager;
use Illuminate\Session\SessionManager;

class LogoutService
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
     *
     */
    public function logout(): void
    {
        $this->authManager->guard()->logout();
        $this->sessionManager->invalidate();
        $this->sessionManager->regenerate();
    }
}
