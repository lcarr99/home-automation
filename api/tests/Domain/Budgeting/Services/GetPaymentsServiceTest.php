<?php

namespace Tests\Domain\Budgeting\Services;

use App\Domain\Budgeting\Instructions\GetPaymentsInstructions;
use App\Domain\Budgeting\Services\GetPaymentsService;
use App\Models\Payment;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GetPaymentsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testUserPaymentsCanBeReturnedInACollection(): void
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

        $getPaymentsService = app(GetPaymentsService::class);
        $payments = $getPaymentsService->getPayments(new GetPaymentsInstructions($user));

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $payments);
        $this->assertCount(1, $payments);
        $this->assertTrue($payments->contains($payment));
    }

    public function testPaymentsThatDoNotBelongToTheUserAreNotReturned(): void
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
            'amount' => '2.00',
            'description' => 'users payment',
        ]);

        $anotherUsersPayment = Payment::query()->create([
            'user_id' => $anotherUser->id,
            'type' => 'outgoing',
            'amount' => '4.00',
            'description' => 'another users payment',
        ]);

        $getPaymentsService = app(GetPaymentsService::class);
        $payments = $getPaymentsService->getPayments(new GetPaymentsInstructions($user));

        $this->assertCount(1, $payments);
        $this->assertTrue($payments->contains($usersPayment));
        $this->assertFalse($payments->contains($anotherUsersPayment));
    }

    public function testSoftDeletedPaymentsAreNotReturned(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@test.co.uk',
            'password' => Hash::make('test'),
        ]);

        $activePayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '2.00',
            'description' => 'active payment',
        ]);

        $deletedPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'outgoing',
            'amount' => '4.00',
            'description' => 'deleted payment',
        ]);

        $deletedPayment->delete();

        $getPaymentsService = app(GetPaymentsService::class);
        $payments = $getPaymentsService->getPayments(new GetPaymentsInstructions($user));

        $this->assertCount(1, $payments);
        $this->assertTrue($payments->contains($activePayment));
        $this->assertFalse($payments->contains($deletedPayment));
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
            'amount' => '2.00',
            'description' => 'included payment',
        ]);
        $this->setPaymentDates($includedPayment, new DateTimeImmutable('2026-04-10 09:00:00'));

        $excludedBeforeDateFrom = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '3.00',
            'description' => 'excluded before',
        ]);
        $this->setPaymentDates($excludedBeforeDateFrom, new DateTimeImmutable('2026-04-04 09:00:00'));

        $excludedAfterDateTo = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'outgoing',
            'amount' => '4.00',
            'description' => 'excluded after',
        ]);
        $this->setPaymentDates($excludedAfterDateTo, new DateTimeImmutable('2026-04-21 09:00:00'));

        $getPaymentsService = app(GetPaymentsService::class);
        $payments = $getPaymentsService->getPayments(
            new GetPaymentsInstructions(
                $user,
                new DateTimeImmutable('2026-04-05'),
                new DateTimeImmutable('2026-04-20')
            )
        );

        $this->assertCount(1, $payments);
        $this->assertTrue($payments->contains($includedPayment));
        $this->assertFalse($payments->contains($excludedBeforeDateFrom));
        $this->assertFalse($payments->contains($excludedAfterDateTo));
    }

    public function testWhenDateFiltersAreNotSetPaymentsFromTheCurrentMonthAreReturned(): void
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
            'amount' => '2.00',
            'description' => 'current month payment',
        ]);
        $this->setPaymentDates($currentMonthPayment, $currentMonthPaymentDate);

        $previousMonthPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'incoming',
            'amount' => '3.00',
            'description' => 'previous month payment',
        ]);
        $this->setPaymentDates($previousMonthPayment, $previousMonthPaymentDate);

        $nextMonthPayment = Payment::query()->create([
            'user_id' => $user->id,
            'type' => 'outgoing',
            'amount' => '4.00',
            'description' => 'next month payment',
        ]);
        $this->setPaymentDates($nextMonthPayment, $nextMonthPaymentDate);

        $getPaymentsService = app(GetPaymentsService::class);
        $payments = $getPaymentsService->getPayments(
            new GetPaymentsInstructions($user)
        );

        $this->assertCount(1, $payments);
        $this->assertTrue($payments->contains($currentMonthPayment));
        $this->assertFalse($payments->contains($previousMonthPayment));
        $this->assertFalse($payments->contains($nextMonthPayment));
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
