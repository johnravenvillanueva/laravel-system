<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_opening_their_notification_marks_it_read_and_opens_its_order(): void
    {
        [$customer, $order] = $this->createCustomerOrder();
        $notification = Notification::create([
            'customer_id' => $customer->customer_id,
            'order_id' => $order->order_id,
            'title' => 'Order updated',
            'message' => 'Your order is confirmed.',
            'type' => 'order_update',
            'is_read' => false,
        ]);

        $this->actingAs($customer, 'customer')
            ->post(route('customer.notifications.open', $notification->notification_id))
            ->assertRedirect(route('customer.orders.show', $order->order_id));

        $this->assertDatabaseHas('notifications', [
            'notification_id' => $notification->notification_id,
            'is_read' => true,
        ]);
    }

    public function test_admin_opening_their_notification_marks_it_read_and_opens_its_order(): void
    {
        $admin = Admin::create([
            'email' => 'notification-admin@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Notification',
            'last_name' => 'Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);
        [$customer, $order] = $this->createCustomerOrder();
        $notification = Notification::create([
            'admin_id' => $admin->admin_id,
            'order_id' => $order->order_id,
            'title' => 'New order',
            'message' => 'A customer placed an order.',
            'type' => 'new_order',
            'is_read' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.notifications.open', $notification->notification_id))
            ->assertRedirect(route('admin.orders.show', $order->order_id));

        $this->assertDatabaseHas('notifications', [
            'notification_id' => $notification->notification_id,
            'is_read' => true,
        ]);
    }

    private function createCustomerOrder(): array
    {
        $customer = Customer::create([
            'email' => 'notification-customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Notification',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);
        $order = Order::create([
            'reference_code' => Order::generateReferenceCode(),
            'customer_id' => $customer->customer_id,
            'order_date' => now(),
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00:00',
            'status' => 'pending',
            'total_amount' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        return [$customer, $order];
    }
}
