<?php

namespace App\Domain\Budgeting\Services;

use App\Domain\Budgeting\Instructions\GetPaymentsInstructions;
use Illuminate\Database\Eloquent\Collection;

class GetPaymentsService
{
    /**
     * @param GetPaymentsInstructions $instructions
     * @return Collection
     */
    public function getPayments(GetPaymentsInstructions $instructions): Collection
    {
        $paymentsQuery = $instructions
            ->getUser()
            ->payments();

        $dateFrom = $instructions->getDateFrom();
        $dateTo = $instructions->getDateTo();

        if ($dateFrom !== null) {
            $paymentsQuery->where('created_at', '>=', $dateFrom->setTime(0, 0));
        }

        if ($dateTo !== null) {
            $paymentsQuery->where('created_at', '<=', $dateTo->setTime(23, 59, 59));
        }

        if ($dateFrom === null && $dateTo === null) {
            $paymentsQuery->whereBetween('created_at', [
                date('Y-m-01 00:00:00'),
                date('Y-m-t 23:59:59'),
            ]);
        }

        return $paymentsQuery->get();
    }
}
