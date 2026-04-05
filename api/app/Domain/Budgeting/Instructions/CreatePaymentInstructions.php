<?php

namespace App\Domain\Budgeting\Instructions;

use App\Domain\Budgeting\PaymentType;
use App\Http\Validation\CreatePaymentValidation;
use App\Models\User;

class CreatePaymentInstructions
{
    public function __construct(
        private User $user,
        private PaymentType $paymentType,
        private string $amount,
        private string $description
    ) {}

    /**
     * @param CreatePaymentValidation $request
     * @return static
     */
    public static function fromRequest(CreatePaymentValidation $request): self
    {
        $validatedData = $request->validated();
        return new self(
            $request->user(),
            PaymentType::from($validatedData['type']),
            $validatedData['amount'],
            $validatedData['description']
        );
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return PaymentType
     */
    public function getPaymentType(): PaymentType
    {
        return $this->paymentType;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
