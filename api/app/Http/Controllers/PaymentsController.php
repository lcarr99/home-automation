<?php

namespace App\Http\Controllers;

use App\Domain\Budgeting\Exceptions\PaymentException;
use App\Domain\Budgeting\Instructions\CreatePaymentInstructions;
use App\Domain\Budgeting\Instructions\DeletePaymentInstructions;
use App\Domain\Budgeting\Services\CreatePaymentService;
use App\Domain\Budgeting\Services\DeletePaymentService;
use App\Http\Resources\PaymentResource;
use App\Http\Validation\CreatePaymentValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * @param Request $request
     * @param DeletePaymentService $service
     * @return Response
     */
    public function deletePayment(Request $request, DeletePaymentService $service): Response
    {
        try {
            $service->deletePayment(
                DeletePaymentInstructions::fromRequest($request)
            );
            return new Response(status: 204);
        } catch (PaymentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
