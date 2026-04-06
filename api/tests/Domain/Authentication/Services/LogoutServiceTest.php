<?php

namespace Tests\Domain\Authentication\Services;

use App\Domain\Authentication\Instructions\LogoutInstructions;
use App\Domain\Authentication\Services\LogoutService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthenticatedUserCanLogout(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user);

        $logoutService = app(LogoutService::class);
        $logoutService->logout();

        $this->assertGuest();
    }
}
