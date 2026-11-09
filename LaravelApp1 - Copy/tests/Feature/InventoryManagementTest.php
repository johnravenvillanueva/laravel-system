<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = Admin::create([
            'email' => 'inventory@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Inventory',
            'last_name' => 'Manager',
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'admin');
    }

    public function test_bulk_stock_count_updates_quantities_and_records_each_change(): void
    {
        $milk = InventoryItem::create([
            'name' => 'Milk',
            'category' => 'Dairy',
            'quantity' => 2.50,
            'unit_cost' => 14.00,
            'unit' => 'L',
            'min_threshold' => 1,
        ]);
        $cups = InventoryItem::create([
            'name' => 'Cups',
            'category' => 'Supplies',
            'quantity' => 8,
            'unit' => 'pcs',
            'min_threshold' => 2,
        ]);

        $this->post(route('admin.inventory.bulk-update'), [
            'selected' => [$milk->inventory_id, $cups->inventory_id],
            'quantities' => [
                $milk->inventory_id => 3.50,
                $cups->inventory_id => 0,
            ],
            'notes' => 'Monthly physical count',
        ])->assertRedirect();

        $this->assertDatabaseHas('inventory_items', [
            'inventory_id' => $milk->inventory_id,
            'quantity' => '3.50',
        ]);
        $this->assertDatabaseHas('inventory_items', [
            'inventory_id' => $cups->inventory_id,
            'quantity' => '0.00',
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_id' => $milk->inventory_id,
            'transaction_type' => 'add',
            'quantity' => '1.00',
            'reference_type' => 'bulk_stocktake',
            'notes' => 'Bulk stock count adjustment from 2.50 to 3.50. Reason: Monthly physical count',
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_id' => $cups->inventory_id,
            'transaction_type' => 'deduct',
            'quantity' => '8.00',
            'reference_type' => 'bulk_stocktake',
        ]);
    }

    public function test_inventory_report_uses_recorded_unit_cost_and_discloses_uncosted_items(): void
    {
        InventoryItem::create([
            'name' => 'Coffee Beans',
            'category' => 'Coffee',
            'quantity' => 2.50,
            'unit_cost' => 14.00,
            'unit' => 'kg',
            'min_threshold' => 1,
        ]);
        InventoryItem::create([
            'name' => 'Paper Cups',
            'category' => 'Supplies',
            'quantity' => 100,
            'unit' => 'pcs',
            'min_threshold' => 20,
        ]);

        $this->get(route('admin.reports.inventory'))
            ->assertOk()
            ->assertSee('₱35.00')
            ->assertSee('1 item(s) have no unit cost');
    }
}
