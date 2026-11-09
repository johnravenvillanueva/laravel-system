<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OrderPickupCapacity
{
    public const MAX_ORDERS_PER_SLOT = 5;

    public static function ensureAvailable(string $pickupDate, string $pickupTime): void
    {
        $pickupTime = $pickupTime . ':00';

        DB::table('order_pickup_slots')->insertOrIgnore([
            'pickup_date' => $pickupDate,
            'pickup_time' => $pickupTime,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $slot = DB::table('order_pickup_slots')
            ->where('pickup_date', $pickupDate)
            ->where('pickup_time', $pickupTime)
            ->lockForUpdate()
            ->first();

        if (! $slot) {
            throw new RuntimeException('Unable to lock the selected pickup slot.');
        }

        $orderCount = Order::query()
            ->where('pickup_date', $pickupDate)
            ->whereIn('pickup_time', [$pickupTime, substr($pickupTime, 0, 5)])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($orderCount >= self::MAX_ORDERS_PER_SLOT) {
            throw ValidationException::withMessages([
                'pickup_time' => 'This pickup time is full. Please choose another date or time.',
            ]);
        }
    }
}
