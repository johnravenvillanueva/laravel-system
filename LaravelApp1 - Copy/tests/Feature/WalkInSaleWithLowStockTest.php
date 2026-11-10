<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalkInSaleWithLowStockTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Product $product;
    private InventoryItem $inventoryItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'email' => 'walk-in-stock@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Walk-in',
            'last_name' => 'Staff',
            'role' => 'staff',
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Drinks']);
        $this->product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Low Stock Drink',
            'price' => 100,
            'is_available' => true,
        ]);
        $this->inventoryItem = InventoryItem::create([
            'name' => 'Limited Ingredient',
            'category' => 'Ingredients',
            'quantity' => 0,
            'unit_cost' => 1,
            'unit' => 'kg',
            'min_threshold' => 1,
        ]);
        ProductIngredient::create([
            'product_id' => $this->product->product_id,
            'inventory_id' => $this->inventoryItem->inventory_id,
            'quantity' => 0.25,
        ]);
    }

    public function test_walk_in_sale_is_rejected_when_ingredient_stock_is_insufficient(): void
    {
        $this->assertFalse($this->product->isOrderable());

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.sales.create'))
            ->assertOk()
            ->assertSee($this->product->name)
            ->assertSee('Out of stock')
            ->assertSee('data-orderable="false"', false);

        $this->postWalkInSale()
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseHas('inventory_items', [
            'inventory_id' => $this->inventoryItem->inventory_id,
            'quantity' => '0.00',
        ]);
    }

    public function test_walk_in_sale_succeeds_when_ingredients_are_available(): void
    {
        $this->inventoryItem->update(['quantity' => 1]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.sales.create'))
            ->assertOk()
            ->assertSee($this->product->name)
            ->assertSee('data-orderable="true"', false);

        $this->postWalkInSale()->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'transaction_type' => 'walk-in',
            'total_amount' => '100.00',
        ]);
        $this->assertDatabaseHas('inventory_items', [
            'inventory_id' => $this->inventoryItem->inventory_id,
            'quantity' => '0.75',
        ]);
    }

    private function postWalkInSale()
    {
        return $this->actingAs($this->admin, 'admin')
            ->post(route('admin.sales.store'), [
                'items' => json_encode([[
                    'id' => $this->product->product_id,
                    'qty' => 1,
                    'price' => 100,
                ]]),
                'payment_method' => 'cash',
            ]);
    }
}
