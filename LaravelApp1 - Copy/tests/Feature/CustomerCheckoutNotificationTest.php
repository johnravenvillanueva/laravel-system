<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCheckoutNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_an_order_when_there_are_no_admin_accounts(): void
    {
        $customer = Customer::create([
            'email' => 'checkout-customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Checkout',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Coffee']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte',
            'price' => 100,
            'is_available' => true,
        ]);

        $this->actingAs($customer, 'customer')
            ->withSession([
                'customer_cart' => [
                    $product->product_id => [
                        'name' => $product->name,
                        'price' => 100,
                        'qty' => 1,
                    ],
                ],
            ])
            ->post(route('customer.checkout.store'), [
                'pickup_date' => now()->addDays(2)->toDateString(),
                'pickup_time' => '10:00',
                'payment_method' => 'cash',
            ])
            ->assertRedirect(route('customer.orders'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'customer_id' => $customer->customer_id,
            'title' => 'Order Placed',
        ]);
    }
}
