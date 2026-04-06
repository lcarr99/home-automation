<?php

namespace App\Http\Controllers;

use App\Domain\Authentication\Services\LogoutService;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    /**
     * @param LogoutService $service
     * @return Response
     */
    public function logout(LogoutService $service): Response
    {
        $service->logout();
        return new Response(status: 204);
    }
}
