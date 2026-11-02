<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{

    public function index(Request $request)
    {
        $query = Sale::with(['staff', 'customer', 'items.product']);

        if ($from = $request->query('from')) {
            $query->whereDate('sale_date', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('sale_date', '<=', $to);
        }

        $sales = $query->latest('sale_date')->get();

        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::with('category')->available()->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('first_name')->get();

        return view('admin.sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items'          => ['required', 'string'], // JSON string from POS
            'payment_method' => ['required', 'in:cash,gcash'],
            'customer_id'    => ['nullable', 'exists:customers,customer_id'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        // Decode POS items: [{"id":1,"qty":2,"price":145}, ...]
        $items = json_decode($data['items'], true);

        if (! is_array($items) || empty($items)) {
            return back()->with('error', 'Please add at least one item to the sale.');
        }

        $staffId = Auth::guard('admin')->id();
        $total = collect($items)->sum(fn($i) => $i['price'] * $i['qty']);

        $sale = DB::transaction(function () use ($data, $items, $total, $staffId) {

            // 1. Create the sale
            $sale = Sale::create([
                'staff_id'         => $staffId,
                'customer_id'      => $data['customer_id'] ?? null,
                'sale_date'        => now(),
                'total_amount'     => $total,
                'payment_method'   => $data['payment_method'],
                'transaction_type' => 'walk-in',
            ]);

                       // 2. Save each line item + deduct inventory
            foreach ($items as $row) {
                $product = Product::find($row['id']);
                if (! $product) continue;

                SaleItem::create([
                    'sale_id'    => $sale->sale_id,
                    'product_id' => $product->product_id,
                    'quantity'   => $row['qty'],
                    'unit_price' => $row['price'],
                    'subtotal'   => $row['price'] * $row['qty'],
                ]);

                // 👇 Call the new centralized logic from the Product model
                $product->deductInventory($row['qty'], 'sale', $sale->sale_id, $staffId);
            }

            return $sale;
        });

        return redirect()
            ->route('admin.sales.show', $sale->sale_id)
            ->with('success', 'Sale recorded — ' . $sale->sale_id);
    }

    public function show(int $id)
    {
        $sale = Sale::with(['items.product', 'staff', 'customer', 'referenceOrder'])
            ->findOrFail($id);

        return view('admin.sales.show', compact('sale'));
    }


    protected function deductItem(string $itemName, float $qty, Sale $sale, int $staffId): void
    {
        $item = InventoryItem::where('name', $itemName)->first();
        if (! $item) {
            return; // no matching inventory — skip silently
        }

        $newQty = max(0, (float) $item->quantity - $qty);
        $item->update(['quantity' => $newQty]);

        InventoryTransaction::create([
            'inventory_id'     => $item->inventory_id,
            'transaction_type' => 'deduct',
            'quantity'         => $qty,
            'reference_type'   => 'sale',
            'reference_id'     => $sale->sale_id,
            'performed_by'     => $staffId,
            'transaction_date' => now(),
            'notes'            => 'Auto-deducted from sale #' . $sale->sale_id,
        ]);
    }
}