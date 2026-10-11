<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('email', 'admin@issang.test')->first();
        $ben   = Admin::where('email', 'ben@issang.test')->first();

        $dan   = Customer::where('email', 'dan@issang.test')->first();
        $ella  = Customer::where('email', 'ella@issang.test')->first();

        $order1 = Order::where('reference_code', 'IM-00001')->first();
        $order2 = Order::where('reference_code', 'IM-00002')->first();
        $order4 = Order::where('reference_code', 'IM-00004')->first();

        $welcome    = Announcement::where('title', 'Welcome to Issang Macchiatos Online!')->first();
        $bogoPromo  = Announcement::where('title', 'Buy 1 Get 1 on Spanish Latte!')->first();

        /* -------------------------------------------------
         |  Customer notifications
         |-------------------------------------------------*/

        // Dan — order confirmed
        Notification::create([
            'customer_id' => $dan->customer_id,
            'order_id'    => $order1->order_id,
            'title'       => 'Order Confirmed',
            'message'     => 'Your pre-order IM-00001 has been confirmed. We\'ll have it ready for pickup at your chosen time.',
            'type'        => 'order_update',
            'is_read'     => false,
        ]);

        // Dan — order is ready for pickup
        Notification::create([
            'customer_id' => $dan->customer_id,
            'order_id'    => $order4->order_id,
            'title'       => 'Your Order is Ready!',
            'message'     => 'IM-00004 is ready for pickup. Please come by the counter and show your order number.',
            'type'        => 'order_update',
            'is_read'     => false,
        ]);

        // Ella — order received
        Notification::create([
            'customer_id' => $ella->customer_id,
            'order_id'    => $order2->order_id,
            'title'       => 'Order Received',
            'message'     => 'Thanks for your pre-order IM-00002. We\'re preparing it now.',
            'type'        => 'order_update',
            'is_read'     => true,
        ]);

        // Ella — promo announcement
        Notification::create([
            'customer_id'     => $ella->customer_id,
            'announcement_id' => $bogoPromo->announcement_id,
            'title'           => 'Buy 1 Get 1 on Spanish Latte!',
            'message'         => 'Every Friday this month, buy one Spanish Latte and get another one free.',
            'type'            => 'promo',
            'is_read'         => false,
        ]);

        // Dan — welcome announcement
        Notification::create([
            'customer_id'     => $dan->customer_id,
            'announcement_id' => $welcome->announcement_id,
            'title'           => 'Welcome to Issang Macchiatos Online!',
            'message'         => 'You can now pre-order your favorite drinks and pastries online.',
            'type'            => 'announcement',
            'is_read'         => true,
        ]);

        /* -------------------------------------------------
         |  Admin/Staff notifications
         |-------------------------------------------------*/

        // Ben — new order alert
        Notification::create([
            'admin_id' => $ben->admin_id,
            'order_id' => $order2->order_id,
            'title'    => 'New Pre-Order Received',
            'message'  => 'Ella Santos placed order IM-00002 for pickup at 2:30 PM today.',
            'type'     => 'new_order',
            'is_read'  => false,
        ]);

        // Admin — low stock alert
        Notification::create([
            'admin_id' => $admin->admin_id,
            'title'    => 'Low Stock Warning',
            'message'  => 'Whole Milk is running low (3 L remaining, threshold is 8 L). Please restock soon.',
            'type'     => 'low_stock',
            'is_read'  => false,
        ]);

        Notification::create([
            'admin_id' => $admin->admin_id,
            'title'    => 'Low Stock Warning',
            'message'  => 'Matcha Powder is running low (2 kg remaining, threshold is 4 kg). Please restock soon.',
            'type'     => 'low_stock',
            'is_read'  => false,
        ]);

        // Ben — low stock alert for paper lids
        Notification::create([
            'admin_id' => $ben->admin_id,
            'title'    => 'Low Stock Warning',
            'message'  => 'Paper Lids (16oz) is running low (80 pcs remaining, threshold is 100 pcs).',
            'type'     => 'low_stock',
            'is_read'  => true,
        ]);
    }
}