<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductInventoryAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_with_insufficient_ingredients_are_unavailable_on_the_menu_and_cannot_be_added(): void
    {
        $product = $this->createProductWithIngredientStock(0, 1);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Out of stock');

        $this->post(route('customer.guest.cart.add', $product->product_id), ['quantity' => 1])
            ->assertSessionHas('error');

        $this->assertSessionHasNoGuestCart();
    }

    public function test_guest_checkout_rejects_a_product_that_went_out_of_stock_after_being_added(): void
    {
        $product = $this->createProductWithIngredientStock(0, 1);

        $this->withSession([
            'guest_cart' => [
                $product->product_id => [
                    'name' => $product->name,
                    'price' => 100,
                    'qty' => 1,
                    'note' => '',
                ],
            ],
        ])->post(route('customer.guest.place'), [
            'guest_name' => 'Guest Customer',
            'guest_phone' => '09123456789',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00',
            'payment_method' => 'cash',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_registered_customer_cannot_add_more_than_available_ingredient_stock(): void
    {
        $customer = Customer::create([
            'email' => 'stock-customer@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Stock',
            'last_name' => 'Customer',
            'phone' => '09123456789',
            'is_guest' => false,
            'is_active' => true,
        ]);
        $product = $this->createProductWithIngredientStock(1, 0.6);

        $this->actingAs($customer, 'customer')
            ->post(route('customer.cart.add', $product->product_id), ['quantity' => 2])
            ->assertSessionHas('error');

        $this->assertSessionHasNoCustomerCart();
    }

    public function test_pending_orders_reserve_their_ingredient_stock(): void
    {
        $product = $this->createProductWithIngredientStock(1, 1);
        $order = Order::create([
            'reference_code' => Order::generateReferenceCode(),
            'guest_name' => 'Existing Guest',
            'guest_phone' => '09123456789',
            'order_date' => now(),
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00:00',
            'status' => 'pending',
            'total_amount' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
        ]);
        $product->load('ingredients.inventoryItem');

        $this->assertFalse($product->isOrderable());
    }

    private function createProductWithIngredientStock(float $stock, float $perProduct): Product
    {
        $category = Category::create(['name' => 'Drinks']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Inventory Test Drink',
            'price' => 100,
            'is_available' => true,
        ]);
        $inventoryItem = InventoryItem::create([
            'name' => 'Test Ingredient',
            'category' => 'Ingredients',
            'quantity' => $stock,
            'unit_cost' => 1,
            'unit' => 'kg',
            'min_threshold' => 0,
        ]);
        ProductIngredient::create([
            'product_id' => $product->product_id,
            'inventory_id' => $inventoryItem->inventory_id,
            'quantity' => $perProduct,
        ]);

        return $product;
    }

    private function assertSessionHasNoGuestCart(): void
    {
        $this->assertEmpty(session('guest_cart', []));
    }

    private function assertSessionHasNoCustomerCart(): void
    {
        $this->assertEmpty(session('customer_cart', []));
    }
}
