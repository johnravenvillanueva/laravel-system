<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'email' => 'reports@example.test',
            'password_hash' => bcrypt('password'),
            'first_name' => 'Report',
            'last_name' => 'Manager',
            'role' => 'admin',
        ]);

        $this->actingAs($this->admin, 'admin');
    }

    public function test_sales_report_applies_custom_dates_and_payment_filters_to_metrics_and_register(): void
    {
        $category = Category::create(['name' => 'Coffee']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Caramel Latte',
            'price' => 100,
            'is_available' => true,
        ]);

        $currentSale = $this->createSale(now()->subDay(), 100, 'cash', 'walk-in');
        SaleItem::create([
            'sale_id' => $currentSale->sale_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => 50,
            'subtotal' => 100,
        ]);

        $otherCurrentSale = $this->createSale(now()->subDay(), 200, 'gcash', 'pre-order');
        SaleItem::create([
            'sale_id' => $otherCurrentSale->sale_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);

        $this->createSale(now()->subDays(4), 50, 'cash', 'walk-in');

        $this->get(route('admin.reports.sales', [
            'range' => 'custom',
            'from' => now()->subDays(2)->toDateString(),
            'to' => now()->toDateString(),
            'payment' => 'cash',
        ]))
            ->assertOk()
            ->assertSee('₱100.00')
            ->assertSee('+100.0%')
            ->assertSee('Caramel Latte')
            ->assertSee('Walk-in');
    }

    public function test_sales_report_exports_filtered_transactions_as_csv(): void
    {
        $sale = $this->createSale(now()->subDay(), 125, 'cash', 'walk-in');

        $response = $this->get(route('admin.reports.sales', [
            'range' => 'custom',
            'from' => now()->subDays(2)->toDateString(),
            'to' => now()->toDateString(),
            'payment' => 'cash',
            'export' => 'csv',
        ]));

        $response->assertOk()
            ->assertDownload('sales-report-'.now()->subDays(2)->toDateString().'-to-'.now()->toDateString().'.csv');

        $content = $response->streamedContent();
        $this->assertStringContainsString((string) $sale->sale_id, $content);
        $this->assertStringContainsString('125.00', $content);
    }

    public function test_inventory_report_can_filter_low_stock_items_and_export_current_filter(): void
    {
        InventoryItem::create([
            'name' => 'Low Milk',
            'category' => 'Dairy',
            'quantity' => 1,
            'unit_cost' => 20,
            'unit' => 'L',
            'min_threshold' => 3,
        ]);
        InventoryItem::create([
            'name' => 'Cup Stock',
            'category' => 'Supplies',
            'quantity' => 100,
            'unit_cost' => 1,
            'unit' => 'pcs',
            'min_threshold' => 20,
        ]);

        $this->get(route('admin.reports.inventory', ['status' => 'low']))
            ->assertOk()
            ->assertSee('Low Milk')
            ->assertDontSee('Cup Stock')
            ->assertSee('Known stock value');

        $response = $this->get(route('admin.reports.inventory', [
            'status' => 'low',
            'export' => 'csv',
        ]));

        $response->assertOk()->assertDownload('inventory-report-'.now()->toDateString().'.csv');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Low Milk', $content);
        $this->assertStringNotContainsString('Cup Stock', $content);
    }

    private function createSale($date, float $amount, string $payment, string $type): Sale
    {
        return Sale::create([
            'staff_id' => $this->admin->admin_id,
            'sale_date' => $date,
            'total_amount' => $amount,
            'payment_method' => $payment,
            'transaction_type' => $type,
        ]);
    }
}
