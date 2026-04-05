<?php

namespace Tests\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testA204StatusCodeIsReturnedUponSuccessfulLogin(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'test@test.co.uk',
            'password' => 'test',
        ])->assertNoContent();
    }

    public function testA422StatusCodeIsReturnedWhenUserNotFound(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
        ]);


        $this->postJson('/api/login', [
            'email' => 'test@test.co.uk',
            'password' => 'test',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('email');
    }

    public function testEmailIsARequiredField(): void
    {
        $this->postJson('/api/login')
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('email');
    }

    public function testEmailMustBeAValidEmail(): void
    {
        $this->postJson('/api/login', [
            'email' => 'test'
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('email');
    }

    public function testPasswordIsARequiredField(): void
    {
        $this->postJson('/api/login')
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('password');
    }
}
