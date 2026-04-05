<?php

namespace Tests\Domain\Budgeting\Services;

use App\Domain\Budgeting\Instructions\CreatePaymentInstructions;
use App\Domain\Budgeting\PaymentType;
use App\Domain\Budgeting\Services\CreatePaymentService;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreatePaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testIncomingPaymentCanBeCreated(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $createPaymentService = app(CreatePaymentService::class);
        $payment = $createPaymentService->createPayment(new CreatePaymentInstructions($user, PaymentType::INCOMING, '2.00', 'Test Incoming'));

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'type' => PaymentType::INCOMING,
            'amount' => '2.00',
            'description' => 'Test Incoming',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
    }

    public function testOutgoingPaymentCanBeCreated(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $createPaymentService = app(CreatePaymentService::class);
        $payment = $createPaymentService->createPayment(new CreatePaymentInstructions($user, PaymentType::OUTGOING, '2.00', 'Test Incoming'));

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'type' => PaymentType::OUTGOING,
            'amount' => '2.00',
            'description' => 'Test Incoming',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
    }
}
