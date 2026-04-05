<?php

namespace Tests\Domain\Authentication\Services;

use App\Domain\Authentication\Exceptions\LoginException;
use App\Domain\Authentication\Instructions\LoginInstructions;
use App\Domain\Authentication\Services\LoginService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLoginIfCredentialsMatch(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $loginInstructions = new LoginInstructions('test@test.co.uk', 'test');
        $loginService = app(LoginService::class);
        $loginService->login($loginInstructions);

        $this->assertAuthenticatedAs($user);
    }

    public function testLoginExceptionIsThrownIfEmailIsWrong(): void
    {
        $this->expectException(LoginException::class);
        $this->expectExceptionMessage('Credentials not found');

        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $loginInstructions = new LoginInstructions('test@test.co.u', 'test');
        $loginService = app(LoginService::class);
        $loginService->login($loginInstructions);
    }

    public function testLoginExceptionIsThrownIfPasswordIsWrong(): void
    {
        $this->expectException(LoginException::class);
        $this->expectExceptionMessage('Credentials not found');

        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $loginInstructions = new LoginInstructions('test@test.co.uk', 'tes');
        $loginService = app(LoginService::class);
        $loginService->login($loginInstructions);
    }
}
