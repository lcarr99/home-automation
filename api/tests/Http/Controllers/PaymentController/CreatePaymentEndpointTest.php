<?php

namespace Tests\Http\Controllers;

use App\Domain\Budgeting\PaymentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreatePaymentEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function testTypeIsARequiredField(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments')
            ->assertUnprocessable()
            ->assertInvalid(['type']);
    }

    public function testTypeMustBeEitherIncomingOrOutgoing(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'type' => 'testEnum'
            ])
            ->assertUnprocessable()
            ->assertInvalid(['type']);
    }

    public function testAmountIsARequiredField(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments')
            ->assertUnprocessable()
            ->assertInvalid(['amount']);
    }

    public function testAmountMustBeTwoDecimalPlaces(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'amount' => '123.234'
            ])
            ->assertUnprocessable()
            ->assertInvalid(['amount']);
    }

    public function testAmountMustBeLessThanOrEqualToMaximumAmount(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'amount' => '10000000.00'
            ])
            ->assertUnprocessable()
            ->assertInvalid(['amount']);
    }

    public function testAmountMustBeGreaterThanZero(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'amount' => '0.00',
            ])
            ->assertUnprocessable()
            ->assertInvalid(['amount']);
    }

    public function testDescriptionIsARequiredField(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments')
            ->assertUnprocessable()
            ->assertInvalid(['description']);
    }

    public function testDescriptionMustBeAString(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'description' => ['test'],
            ])
            ->assertUnprocessable()
            ->assertInvalid(['description']);
    }

    public function testDescriptionMustBeLessThanOrEqualToOneHundredCharacters(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean ma',
            ])
            ->assertUnprocessable()
            ->assertInvalid(['description']);
    }

    public function testIncomingPaymentCanBeCreated(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'type' => PaymentType::INCOMING,
                'amount' => '100.00',
                'description' => 'Lorem ipsum dolor sit amet',
            ])
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'type' => PaymentType::INCOMING->value,
                    'amount' => '100.00',
                    'description' => 'Lorem ipsum dolor sit amet',
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'type' => PaymentType::INCOMING,
            'amount' => '100.00',
            'description' => 'Lorem ipsum dolor sit amet',
        ]);
    }

    public function testOutgoingPaymentCanBeCreated(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->postJson('api/payments', [
                'type' => PaymentType::OUTGOING,
                'amount' => '100.00',
                'description' => 'Lorem ipsum dolor sit amet',
            ])
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'type' => PaymentType::OUTGOING->value,
                    'amount' => '100.00',
                    'description' => 'Lorem ipsum dolor sit amet',
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'type' => PaymentType::OUTGOING,
            'amount' => '100.00',
            'description' => 'Lorem ipsum dolor sit amet',
        ]);
    }
}
