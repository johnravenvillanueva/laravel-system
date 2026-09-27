<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssangWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_customer_and_admin_login_and_registration_screens(): void
    {
        $this->get('/')->assertOk()->assertSee('CUSTOMER ACCESS');
        $this->get('/login?portal=admin')->assertOk()->assertSee('Admin Login');
        $this->get('/register')->assertOk()->assertSee('PHONE NUMBER')->assertSee('ADDRESS');

        $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'phone' => '09171234567',
            'address' => 'Pinaod, Bulacan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/home');

        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.test',
            'role' => 'user',
            'phone' => '09171234567',
            'address' => 'Pinaod, Bulacan',
        ]);
    }

    public function test_login_portals_enforce_the_matching_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        $customer = User::factory()->create(['role' => 'user']);

        $this->post('/login', [
            'portal' => 'admin',
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/home');
        $this->assertAuthenticatedAs($admin);

        $this->post('/logout');
        $this->post('/login', [
            'portal' => 'admin',
            'email' => $customer->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', [
            'portal' => 'user',
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect('/home');
        $this->assertAuthenticatedAs($customer);
    }

    public function test_customer_can_place_order_and_inventory_stock_is_decremented(): void
    {
        $customer = User::factory()->create([
            'role' => 'user',
            'phone' => '09171234567',
            'address' => 'Pinaod, Bulacan',
        ]);
        $product = Product::create([
            'name' => 'Mango Bliss',
            'category' => 'coffee',
            'description' => 'Mango coffee',
            'price' => 49,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($customer)->get(route('user.dashboard'))->assertOk()->assertSee('Place an order');
        $this->get(route('user.order.create'))->assertOk()->assertSee('Coffee and Pastry');
        $this->get(route('notifications.index'))->assertOk()->assertSee('Pickup updates and cafe news');

        $response = $this->post(route('user.orders.store'), [
            'order_type' => 'coffee',
            'items' => json_encode([['product_id' => $product->id, 'quantity' => 2]]),
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:30',
            'special_instructions' => 'No sugar',
        ]);

        $response->assertRedirect(route('user.orders'));
        $this->get(route('user.orders'))->assertOk()->assertSee('Mango Bliss');
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'order_type' => 'coffee',
            'subtotal' => 98,
            'status' => 'Pending',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertSame(1, Order::where('user_id', $customer->id)->count());

        $this->post(route('user.orders.store'), [
            'order_type' => 'lunch',
            'lunch_description' => 'Chicken adobo with extra spice',
            'items' => '[]',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '12:00',
            'special_instructions' => 'No onions',
        ])->assertRedirect(route('user.orders'));

        $this->assertSame(2, Order::where('user_id', $customer->id)->count());
    }

    public function test_admin_can_complete_walk_in_sale_and_open_management_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '09170000000',
            'address' => 'Issang Macchiatos',
        ]);
        $product = Product::create([
            'name' => 'Butter Croissant',
            'category' => 'pastry',
            'description' => 'Flaky croissant',
            'price' => 49,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Orders Today');
        $this->get(route('notifications.index'))->assertOk()->assertSee('Operations updates');
        $this->get(route('admin.orders'))->assertOk()->assertSee('Customer pickup queue');
        $this->get(route('admin.sales'))->assertOk()->assertSee('Walk-in Sales');
        $this->get(route('admin.inventory'))->assertOk()->assertSee('Butter Croissant');
        $this->get(route('admin.reports'))->assertOk()->assertSee('Sales Reports');

        $response = $this->post(route('admin.sales.store'), [
            'customer_name' => 'Walk-in customer',
            'payment_method' => 'Cash',
            'items' => json_encode([['product_id' => $product->id, 'quantity' => 1]]),
        ]);

        $response->assertRedirect(route('admin.sales'));
        $this->assertDatabaseHas('orders', [
            'user_id' => null,
            'order_type' => 'walk-in',
            'payment_method' => 'Cash',
            'status' => 'Completed',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);

        $order = Order::where('order_type', 'walk-in')->firstOrFail();
        $this->patch(route('admin.orders.status', $order), ['status' => 'Preparing'])
            ->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Preparing']);
    }

    public function test_admin_can_add_edit_and_archive_inventory_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'name' => 'Rose Latte',
            'category' => 'coffee',
            'description' => 'Rose and vanilla latte',
            'price' => 65,
            'stock' => 10,
            'is_active' => '1',
        ])->assertRedirect(route('admin.inventory'));

        $product = Product::where('name', 'Rose Latte')->firstOrFail();
        $this->put(route('admin.inventory.update', $product), [
            'name' => 'Rose Vanilla Latte',
            'category' => 'coffee',
            'description' => 'Updated recipe',
            'price' => 69,
            'stock' => 8,
            'is_active' => '1',
        ])->assertRedirect(route('admin.inventory'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Rose Vanilla Latte', 'stock' => 8]);

        $this->delete(route('admin.inventory.destroy', $product))->assertRedirect(route('admin.inventory'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_active' => false]);
    }
}