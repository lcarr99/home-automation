<?php

namespace Tests\Domain\Budgeting\Services;

use App\Domain\Budgeting\Exceptions\PaymentException;
use App\Domain\Budgeting\Instructions\DeletePaymentInstructions;
use App\Domain\Budgeting\Services\DeletePaymentService;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeletePaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testPaymentCanBeSoftDeleted(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);

        $deletePaymentService = app(DeletePaymentService::class);
        $deletePaymentInstructions = new DeletePaymentInstructions($user, $payment->id);

        $deletePaymentService->deletePayment($deletePaymentInstructions);

        $this->assertSoftDeleted('payments', [
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);
    }

    public function testErrorIsThrownIfPaymentDoesntExist(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage(sprintf('Payment not found by %s', $payment->id + 1));

        $deletePaymentService = app(DeletePaymentService::class);
        $deletePaymentInstructions = new DeletePaymentInstructions($user, $payment->id + 1);

        $deletePaymentService->deletePayment($deletePaymentInstructions);

        $this->assertNotSoftDeleted('payments', [
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);
    }

    public function testErrorIsThrownIfPaymentDoesNotBelongToUser(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $anotherUser = User::query()->create([
            'name' => 'Another Test User',
            'email' => 'anothertest@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $anotherUser->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage(sprintf('Payment not found by %s', $payment->id));

        $deletePaymentService = app(DeletePaymentService::class);
        $deletePaymentInstructions = new DeletePaymentInstructions($user, $payment->id);

        $deletePaymentService->deletePayment($deletePaymentInstructions);

        $this->assertNotSoftDeleted('payments', [
            'user_id' => $anotherUser->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);
    }

    public function testErrorIsThrownIfPaymentIsAlreadySoftDeleted(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);

        $payment->delete();

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage(sprintf('Payment not found by %s', $payment->id));

        $deletePaymentService = app(DeletePaymentService::class);
        $deletePaymentInstructions = new DeletePaymentInstructions($user, $payment->id);

        $deletePaymentService->deletePayment($deletePaymentInstructions);
    }
}
