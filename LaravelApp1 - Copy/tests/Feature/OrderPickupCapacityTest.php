<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Support\OrderPickupCapacity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderPickupCapacityTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_pickup_date_and_time_accepts_five_orders_and_rejects_the_sixth(): void
    {
        $pickupDate = now()->addDays(3)->toDateString();

        for ($number = 1; $number <= OrderPickupCapacity::MAX_ORDERS_PER_SLOT; $number++) {
            DB::transaction(function () use ($pickupDate, $number) {
                OrderPickupCapacity::ensureAvailable($pickupDate, '07:00');
                $this->createOrder($pickupDate, '07:00', $number);
            });
        }

        try {
            DB::transaction(function () use ($pickupDate) {
                OrderPickupCapacity::ensureAvailable($pickupDate, '07:00');
            });
            $this->fail('A sixth order should not be allowed for the same pickup slot.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('pickup_time', $exception->errors());
        }

        DB::transaction(function () use ($pickupDate) {
            OrderPickupCapacity::ensureAvailable($pickupDate, '07:30');
        });
        DB::transaction(function () use ($pickupDate) {
            OrderPickupCapacity::ensureAvailable(\Carbon\Carbon::parse($pickupDate)->addDay()->toDateString(), '07:00');
        });
        $this->assertDatabaseCount('orders', OrderPickupCapacity::MAX_ORDERS_PER_SLOT);
    }

    public function test_cancelled_orders_do_not_consume_pickup_slot_capacity(): void
    {
        $pickupDate = now()->addDays(3)->toDateString();

        for ($number = 1; $number <= OrderPickupCapacity::MAX_ORDERS_PER_SLOT; $number++) {
            DB::transaction(function () use ($pickupDate, $number) {
                OrderPickupCapacity::ensureAvailable($pickupDate, '07:00');
                $this->createOrder(
                    $pickupDate,
                    '07:00',
                    $number,
                    $number === 1 ? 'cancelled' : 'pending'
                );
            });
        }

        DB::transaction(function () use ($pickupDate) {
            OrderPickupCapacity::ensureAvailable($pickupDate, '07:00');
            $this->createOrder($pickupDate, '07:00', 6);
        });

        $this->assertDatabaseCount('orders', OrderPickupCapacity::MAX_ORDERS_PER_SLOT + 1);
    }

    private function createOrder(string $pickupDate, string $pickupTime, int $number, string $status = 'pending'): void
    {
        Order::create([
            'reference_code' => sprintf('TEST-%05d', $number),
            'order_date' => now(),
            'pickup_date' => $pickupDate,
            'pickup_time' => $pickupTime,
            'status' => $status,
            'total_amount' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
    }
}
