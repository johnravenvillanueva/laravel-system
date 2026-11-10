<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_edit_does_not_offer_or_accept_a_staff_role(): void
    {
        $admin = $this->createAdmin('role-admin@example.test', 'admin');
        $customer = Customer::create([
            'email' => 'role-customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Existing',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.edit', ['id' => $customer->customer_id, 'type' => 'customer']))
            ->assertOk()
            ->assertDontSee('name="role"', false);

        $this->put(route('admin.users.customers.update', $customer->customer_id), [
            'first_name' => 'Existing',
            'last_name' => 'Customer',
            'email' => 'role-customer@example.test',
            'phone' => '09123456789',
            'is_active' => '1',
            'role' => 'admin',
        ])->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'first_name' => 'Existing',
        ]);
        $this->assertDatabaseHas('admins', [
            'admin_id' => $admin->admin_id,
            'role' => 'admin',
        ]);
        $this->assertDatabaseCount('admins', 1);
    }

    public function test_admin_can_still_promote_existing_staff_to_admin(): void
    {
        $admin = $this->createAdmin('role-owner@example.test', 'admin');
        $staff = $this->createAdmin('role-staff@example.test', 'staff');

        $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', $staff->admin_id), [
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'role' => 'admin',
                'is_active' => '1',
                'password' => '',
                'password_confirmation' => '',
            ])->assertRedirect(route('admin.users.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('admins', [
            'admin_id' => $staff->admin_id,
            'role' => 'admin',
        ]);
    }

    private function createAdmin(string $email, string $role): Admin
    {
        return Admin::create([
            'email' => $email,
            'password_hash' => bcrypt('password'),
            'first_name' => 'Test',
            'last_name' => ucfirst($role),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
