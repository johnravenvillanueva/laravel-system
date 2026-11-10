<?php

namespace App\Support;

use App\Models\GuestSmsNotification;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GuestSmsSimulator
{
    public static function orderPlaced(Order $order): GuestSmsNotification
    {
        $pickup = $order->pickup_date->format('M d, Y').' at '.Carbon::parse($order->pickup_time)->format('g:i A');
        $message = 'Hi '.$order->guest_name.', we received order '.$order->reference_code
            .'. Status: pending confirmation. Pickup: '.$pickup
            .'. Total: PHP '.number_format((float) $order->total_amount, 2).'.';

        return self::record($order, $message);
    }

    public static function orderStatusChanged(Order $order, string $statusLabel): GuestSmsNotification
    {
        $message = 'Hi '.$order->guest_name.', order '.$order->reference_code
            .' is now '.$statusLabel.'.';

        if ($order->status === 'ready') {
            $message .= ' It is ready for pickup.';
        }

        return self::record($order, $message);
    }

    private static function record(Order $order, string $message): GuestSmsNotification
    {
        return GuestSmsNotification::create([
            'order_id' => $order->order_id,
            'message' => Str::limit($message, 480, ''),
            'delivery_status' => 'simulated',
        ]);
    }
}
