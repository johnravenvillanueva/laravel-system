<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\GuestSmsNotification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestSmsNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_order_creates_a_demo_sms_for_the_entered_phone(): void
    {
        $category = Category::create(['name' => 'Drinks']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Iced Coffee',
            'price' => 80,
            'is_available' => true,
        ]);

        $this->withSession([
            'guest_cart' => [
                $product->product_id => [
                    'name' => $product->name,
                    'price' => 80,
                    'qty' => 1,
                    'note' => '',
                ],
            ],
        ])->post(route('customer.guest.place'), [
            'guest_name' => 'Demo Guest',
            'guest_phone' => '09123456789',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
        ])->assertRedirect(route('customer.guest.confirm'))
            ->assertSessionHas('guest_last_order.phone', '09123456789')
            ->assertSessionHas('guest_last_order.sms');

        $this->assertDatabaseCount('guest_sms_notifications', 1);
        $sms = GuestSmsNotification::firstOrFail();
        $this->assertSame('simulated', $sms->delivery_status);
        $this->assertStringContainsString('Demo Guest', $sms->message);
        $this->get(route('customer.guest.confirm'))
            ->assertOk()
            ->assertSee('SMS notification demo')
            ->assertSee('not sent through a mobile carrier');
    }

    public function test_admin_status_change_creates_an_additional_guest_demo_sms(): void
    {
        $admin = Admin::create([
            'email' => 'guest-sms-admin@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Demo',
            'last_name' => 'Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $order = Order::create([
            'reference_code' => Order::generateReferenceCode(),
            'guest_name' => 'Demo Guest',
            'guest_phone' => '09123456789',
            'order_date' => now(),
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00:00',
            'status' => 'pending',
            'total_amount' => 80,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin')
            ->put(route('admin.orders.status', $order->order_id), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertDatabaseHas('guest_sms_notifications', [
            'order_id' => $order->order_id,
            'delivery_status' => 'simulated',
            'message' => 'Hi Demo Guest, order '.$order->reference_code.' is now Confirmed.',
        ]);
    }
}
