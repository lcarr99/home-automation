<?php

namespace App\Http\Controllers;

use App\Domain\Authentication\Exceptions\LoginException;
use App\Domain\Authentication\Instructions\LoginInstructions;
use App\Domain\Authentication\Services\LoginService;
use Illuminate\Http\Response;
use App\Http\Validation\LoginValidation;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @param LoginValidation $request
     * @param LoginService $service
     * @return Response
     * @throws ValidationException
     */
    public function login(LoginValidation $request, LoginService $service): Response
    {
        try {
            $service->login(LoginInstructions::fromRequest($request));
            return new Response(status: 204);
        } catch (LoginException $loginException) {
            throw ValidationException::withMessages([
                'email' => $loginException->getMessage(),
            ]);
        }
    }
}
