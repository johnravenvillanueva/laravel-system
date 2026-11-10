<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerActivityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_time_since_customers_last_order_and_can_deactivate_account(): void
    {
        $admin = Admin::create([
            'email' => 'activity-admin@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Activity',
            'last_name' => 'Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $customer = Customer::create([
            'email' => 'inactive-customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Inactive',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);
        Order::create([
            'reference_code' => Order::generateReferenceCode(),
            'customer_id' => $customer->customer_id,
            'order_date' => now()->subDays(30),
            'pickup_date' => now()->subDays(29)->toDateString(),
            'pickup_time' => '10:00:00',
            'status' => 'completed',
            'total_amount' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.index', ['role' => 'customer']))
            ->assertOk()
            ->assertSee('Last order')
            ->assertSee('30 days since last order');

        $this->put(route('admin.users.customers.status', $customer->customer_id), [
            'is_active' => 0,
        ])->assertRedirect(route('admin.users.index', ['role' => 'customer']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'is_active' => false,
        ]);

        $this->actingAs($customer->fresh(), 'customer')
            ->get(route('customer.orders'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'This account has been deactivated.');
    }

    public function test_admin_can_see_customers_who_have_never_placed_an_order(): void
    {
        $admin = Admin::create([
            'email' => 'new-user-admin@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'New',
            'last_name' => 'Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);
        Customer::create([
            'email' => 'new-customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'New',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.index', ['role' => 'customer']))
            ->assertOk()
            ->assertSee('No orders yet')
            ->assertSee('Account age:');
    }
}
