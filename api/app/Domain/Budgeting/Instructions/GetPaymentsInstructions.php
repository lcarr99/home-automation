<?php

namespace App\Domain\Budgeting\Instructions;

use App\Http\Validation\GetPaymentsValidation;
use App\Models\User;
use DateTimeImmutable;
use Exception;

class GetPaymentsInstructions
{
    /**
     * @param User $user
     * @param DateTimeImmutable|null $dateFrom
     * @param DateTimeImmutable|null $dateTo
     */
    public function __construct(
        private User $user,
        private ?DateTimeImmutable $dateFrom = null,
        private ?DateTimeImmutable $dateTo = null
    )
    {}

    /**
     * @param GetPaymentsValidation $request
     * @return static
     * @throws Exception
     */
    public static function fromRequest(GetPaymentsValidation $request): self
    {
        $validatedData = $request->validated();

        return new self(
            $request->user(),
            isset($validatedData['dateFrom']) ? new DateTimeImmutable($validatedData['dateFrom']) : null,
            isset($validatedData['dateTo']) ? new DateTimeImmutable($validatedData['dateTo']) : null,
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
     * @return DateTimeImmutable|null
     */
    public function getDateFrom(): ?DateTimeImmutable
    {
        return $this->dateFrom;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDateTo(): ?DateTimeImmutable
    {
        return $this->dateTo;
    }
}
