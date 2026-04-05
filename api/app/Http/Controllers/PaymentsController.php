<?php

namespace App\Http\Controllers;

use App\Domain\Budgeting\Instructions\CreatePaymentInstructions;
use App\Domain\Budgeting\Services\CreatePaymentService;
use App\Http\Resources\PaymentResource;
use App\Http\Validation\CreatePaymentValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PaymentsController extends Controller
{
    /**
     * @param CreatePaymentValidation $request
     * @param CreatePaymentService $service
     * @return JsonResponse
     */
    public function createPayment(
        CreatePaymentValidation $request,
        CreatePaymentService $service
    ): JsonResponse {
        $payment = $service->createPayment(
            CreatePaymentInstructions::fromRequest($request)
        );
        return new PaymentResource($payment)
            ->response()
            ->setStatusCode(201);
    }
}
