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
        $products = Product::with(['category', 'ingredients.inventoryItem'])->available()->orderBy('name')->get();
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

        // Decode POS items: [{"id":1,"qty":2}, ...]
        $items = json_decode($data['items'], true);

        if (! is_array($items) || empty($items) || ! array_is_list($items)) {
            return back()->with('error', 'Please add at least one item to the sale.');
        }

        $quantities = [];
        foreach ($items as $row) {
            if (! is_array($row)
                || ! isset($row['id'], $row['qty'])
                || filter_var($row['id'], FILTER_VALIDATE_INT) === false
                || filter_var($row['qty'], FILTER_VALIDATE_INT) === false
                || (int) $row['qty'] < 1) {
                return back()->with('error', 'The sale contains an invalid product or quantity.');
            }

            $productId = (int) $row['id'];
            $quantities[$productId] = ($quantities[$productId] ?? 0) + (int) $row['qty'];
        }

        $staffId = Auth::guard('admin')->id();

        $sale = DB::transaction(function () use ($data, $quantities, $staffId) {
            $productIds = array_keys($quantities);
            Product::lockInventoryForProducts($productIds);
            $products = Product::with('ingredients.inventoryItem')
                ->whereIn('product_id', $productIds)
                ->get()
                ->keyBy('product_id');

            $total = 0;
            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                if (! $product || ! $product->isOrderable($quantity)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => ($product?->name ?? 'A product')
                            .' does not have enough ingredients in stock for this sale.',
                    ]);
                }

                $total += (float) $product->price * $quantity;
            }

            $sale = Sale::create([
                'staff_id'         => $staffId,
                'customer_id'      => $data['customer_id'] ?? null,
                'sale_date'        => now(),
                'total_amount'     => $total,
                'payment_method'   => $data['payment_method'],
                'transaction_type' => 'walk-in',
            ]);

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                SaleItem::create([
                    'sale_id'    => $sale->sale_id,
                    'product_id' => $product->product_id,
                    'quantity'   => $quantity,
                    'unit_price' => $product->price,
                    'subtotal'   => (float) $product->price * $quantity,
                ]);

                $product->deductInventory($quantity, 'sale', $sale->sale_id, $staffId);
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