<?php

namespace Tests\Http\Controllers\PaymentController;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeletePaymentEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function testPaymentCanBeDeleted(): void
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

        $this->actingAs($user)
            ->delete('/api/payments/' . $payment->id)
            ->assertNoContent();

        $this->assertSoftDeleted('payments', [
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);
    }

    public function test404StatusCodeIsReturnedWhenPaymentDoesntExist(): void
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

        $this->actingAs($user)
            ->delete('/api/payments/' . $payment->id + 1)
            ->assertNotFound();
    }

    public function test404StatusCodeIsReturnedWhenPaymentAlreadySoftDeleted(): void
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

        $this->actingAs($user)
            ->delete('/api/payments/' . $payment->id)
            ->assertNotFound();
    }

    public function test404StatusCodeIsReturnedWhenPaymentDoesNotBelongToUser(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $anotherUser = User::query()->create([
            'name' => 'Another Test',
            'email' => 'anothertest@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'test',
        ]);

        $this->actingAs($anotherUser)
            ->delete('/api/payments/' . $payment->id)
            ->assertNotFound();
    }
}
