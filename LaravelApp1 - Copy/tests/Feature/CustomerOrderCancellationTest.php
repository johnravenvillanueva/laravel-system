<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'email' => 'customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);
        $this->admin = Admin::create([
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_customer_can_cancel_a_pending_order_and_staff_are_notified(): void
    {
        $order = $this->createOrder('pending');
        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.orders.cancel', $order->order_id))
            ->assertRedirect(route('customer.orders.show', $order->order_id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('notifications', [
            'customer_id' => $this->customer->customer_id,
            'order_id' => $order->order_id,
            'title' => 'Order Cancelled',
        ]);
        $this->assertDatabaseHas('notifications', [
            'admin_id' => $this->admin->admin_id,
            'order_id' => $order->order_id,
            'title' => 'Order Cancelled by Customer',
        ]);
    }

    public function test_customer_cannot_cancel_after_admin_accepts_order(): void
    {
        $order = $this->createOrder('pending');
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.status', $order->order_id), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.orders.cancel', $order->order_id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'confirmed',
        ]);
    }

    public function test_customer_cannot_cancel_an_order_owned_by_another_account(): void
    {
        $otherCustomer = Customer::create([
            'email' => 'other@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Other',
            'last_name' => 'Customer',
            'phone' => '09999999999',
            'is_guest' => false,
            'is_active' => true,
        ]);
        $order = $this->createOrder('pending');

        $this->actingAs($otherCustomer, 'customer')
            ->post(route('customer.orders.cancel', $order->order_id))
            ->assertNotFound();

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_completion_records_one_sale_and_deducts_inventory_once(): void
    {
        $category = Category::create(['name' => 'Coffee']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte',
            'price' => 100,
            'is_available' => true,
        ]);
        $inventoryItem = InventoryItem::create([
            'name' => 'Coffee beans',
            'category' => 'Coffee',
            'quantity' => 10,
            'unit_cost' => 5,
            'unit' => 'kg',
            'min_threshold' => 1,
        ]);
        ProductIngredient::create([
            'product_id' => $product->product_id,
            'inventory_id' => $inventoryItem->inventory_id,
            'quantity' => 0.2,
        ]);
        $order = $this->createOrder('ready');
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => 50,
            'subtotal' => 100,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.status', $order->order_id), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseCount('sale_items', 1);
        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseHas('inventory_items', [
            'inventory_id' => $inventoryItem->inventory_id,
            'quantity' => '9.60',
        ]);
        $this->assertDatabaseCount('inventory_transactions', 1);
    }

    private function createOrder(string $status): Order
    {
        return Order::create([
            'reference_code' => Order::generateReferenceCode(),
            'customer_id' => $this->customer->customer_id,
            'order_date' => now(),
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00:00',
            'status' => $status,
            'total_amount' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
    }
}
