<?php

namespace Tests\Http\Controllers\LogoutController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function testA204StatusCodeIsReturnedUponSuccessfulLogout(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user, 'web')
            ->postJson('/api/logout')
            ->assertNoContent();

        $this->assertGuest();
    }

    public function testA401StatusCodeIsReturnedWhenUserIsNotAuthenticated(): void
    {
        $this->postJson('/api/logout')
            ->assertUnauthorized();
    }
}
