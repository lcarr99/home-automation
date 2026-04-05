<?php

namespace App\Domain\Authentication\Instructions;

use App\Http\Validation\LoginValidation;

class LoginInstructions
{
    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(private string $email, private string $password)
    {}

    /**
     * @return static
     */
    public static function fromRequest(LoginValidation $request): self
    {
        return new self($request->validated('email'), $request->validated('password'));
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
