<?php

namespace App\Domain\Budgeting\Instructions;

use App\Models\User;
use Illuminate\Http\Request;

class DeletePaymentInstructions
{
    /**
     * @param User $user
     * @param int $paymentId
     */
    public function __construct(private User $user, private int $paymentId)
    {}

    /**
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): self
    {
        return new self($request->user(), $request->route('id'));
    }

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
