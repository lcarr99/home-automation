<?php

namespace Tests\Http\Controllers\PaymentController;

use App\Models\Payment;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GetPaymentsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function testPaymentsAreReturnedInTheCorrectJsonFormat(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'Lorem ipsum dolor sit amet',
        ]);

        $this->actingAs($user)
            ->getJson('/api/payments')
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $payment->id,
                        'type' => 'incoming',
                        'amount' => '100.00',
                        'description' => 'Lorem ipsum dolor sit amet',
                    ],
                ],
            ]);
    }

    public function testPaymentsCanBeFilteredByDateFromAndDateTo(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $includedPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'included payment',
        ]);
        $this->setPaymentDates($includedPayment, new DateTimeImmutable('2026-04-10 09:00:00'));

        $excludedBeforeDateFrom = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '50.00',
            'description' => 'excluded before',
        ]);
        $this->setPaymentDates($excludedBeforeDateFrom, new DateTimeImmutable('2026-04-04 09:00:00'));

        $excludedAfterDateTo = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'outgoing',
            'amount' => '75.00',
            'description' => 'excluded after',
        ]);
        $this->setPaymentDates($excludedAfterDateTo, new DateTimeImmutable('2026-04-21 09:00:00'));

        $response = $this->actingAs($user)
            ->getJson('/api/payments?dateFrom=2026-04-05&dateTo=2026-04-20')
            ->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $includedPayment->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'included payment',
        ]);
        $response->assertJsonMissing([
            'id' => $excludedBeforeDateFrom->id,
            'description' => 'excluded before',
        ]);
        $response->assertJsonMissing([
            'id' => $excludedAfterDateTo->id,
            'description' => 'excluded after',
        ]);
    }

    public function testDateFromFormatIsValidatedWhenItIsSet(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->getJson('/api/payments?dateFrom=15-04-2026')
            ->assertUnprocessable()
            ->assertInvalid(['dateFrom']);
    }

    public function testDateToFormatIsValidatedWhenItIsSet(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->getJson('/api/payments?dateTo=15-04-2026')
            ->assertUnprocessable()
            ->assertInvalid(['dateTo']);
    }

    public function testDateFromMustBeLessThanOrEqualToDateToWhenBothAreSet(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $this->actingAs($user)
            ->getJson('/api/payments?dateFrom=2026-04-20&dateTo=2026-04-05')
            ->assertUnprocessable()
            ->assertInvalid(['dateFrom', 'dateTo']);
    }

    public function testDateToCanBeGreaterThanOrEqualToDateFromWhenBothAreSet(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'valid range payment',
        ]);
        $this->setPaymentDates($payment, new DateTimeImmutable('2026-04-10 09:00:00'));

        $this->actingAs($user)
            ->getJson('/api/payments?dateFrom=2026-04-05&dateTo=2026-04-20')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $payment->id,
                'type' => 'incoming',
                'amount' => '100.00',
                'description' => 'valid range payment',
            ]);
    }

    public function testPaymentsThatDoNotBelongToTheUserAreExcludedFromTheResponse(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $anotherUser = User::query()->create([
            'name' => 'Another Test User',
            'email' => 'another@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $usersPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'users payment',
        ]);

        $anotherUsersPayment = Payment::query()->create([
            'user_id' => $anotherUser->id,
            'type' => 'outgoing',
            'amount' => '200.00',
            'description' => 'another users payment',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/payments')
            ->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $usersPayment->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'users payment',
        ]);
        $response->assertJsonMissing([
            'id' => $anotherUsersPayment->id,
            'type' => 'outgoing',
            'amount' => '200.00',
            'description' => 'another users payment',
        ]);
    }

    public function testPaymentsThatAreSoftDeletedAreExcludedFromTheResponse(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $activePayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'active payment',
        ]);

        $deletedPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'outgoing',
            'amount' => '200.00',
            'description' => 'deleted payment',
        ]);

        $deletedPayment->delete();

        $response = $this->actingAs($user)
            ->getJson('/api/payments')
            ->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $activePayment->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'active payment',
        ]);
        $response->assertJsonMissing([
            'id' => $deletedPayment->id,
            'type' => 'outgoing',
            'amount' => '200.00',
            'description' => 'deleted payment',
        ]);
    }

    public function testWhenDateFiltersAreNotSetTheEndpointReturnsPaymentsFromTheCurrentMonth(): void
    {
        $now = new DateTimeImmutable('now');
        $currentMonthPaymentDate = $now->modify('first day of this month')->setTime(9, 0, 0);
        $previousMonthPaymentDate = $now->modify('last day of previous month')->setTime(9, 0, 0);
        $nextMonthPaymentDate = $now->modify('first day of next month')->setTime(9, 0, 0);

        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $currentMonthPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'current month payment',
        ]);
        $this->setPaymentDates($currentMonthPayment, $currentMonthPaymentDate);

        $previousMonthPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '90.00',
            'description' => 'previous month payment',
        ]);
        $this->setPaymentDates($previousMonthPayment, $previousMonthPaymentDate);

        $nextMonthPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'outgoing',
            'amount' => '80.00',
            'description' => 'next month payment',
        ]);
        $this->setPaymentDates($nextMonthPayment, $nextMonthPaymentDate);

        $response = $this->actingAs($user)
            ->getJson('/api/payments')
            ->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $currentMonthPayment->id,
            'type' => 'incoming',
            'amount' => '100.00',
            'description' => 'current month payment',
        ]);
        $response->assertJsonMissing([
            'id' => $previousMonthPayment->id,
            'description' => 'previous month payment',
        ]);
        $response->assertJsonMissing([
            'id' => $nextMonthPayment->id,
            'description' => 'next month payment',
        ]);
    }

    private function setPaymentDates(Payment $payment, DateTimeImmutable $dateTime): void
    {
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $payment->forceFill([
            'created_at' => $formattedDateTime,
            'updated_at' => $formattedDateTime,
        ])->saveQuietly();
    }
}
