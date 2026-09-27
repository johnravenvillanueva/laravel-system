<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IssangController extends Controller
{
    public function home()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return redirect()->route($this->isAdmin() ? 'admin.dashboard' : 'user.dashboard');
    }

    public function userDashboard()
    {
        $this->requireCustomer();
        $orders = Order::where('user_id', Auth::id())->latest()->take(3)->get();

        return view('issang.user.dashboard', [
            'orders' => $orders,
            'orderCount' => Order::where('user_id', Auth::id())->count(),
        ]);
    }

    public function createOrder()
    {
        $this->requireCustomer();

        return view('issang.user.place-order', [
            'products' => Product::where('is_active', true)->orderBy('name')->get()->groupBy('category'),
        ]);
    }

    public function storeOrder(Request $request)
    {
        $this->requireCustomer();
        $data = $request->validate([
            'order_type' => ['required', Rule::in(['lunch', 'coffee'])],
            'lunch_description' => ['nullable', 'string', 'max:2000'],
            'items' => ['nullable', 'json'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'special_instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = $this->decodeCart($data['items'] ?? '[]');
        if ($data['order_type'] === 'lunch' && blank($data['lunch_description'])) {
            throw ValidationException::withMessages(['lunch_description' => 'Describe your lunch before placing the order.']);
        }
        if ($data['order_type'] === 'coffee' && $cart === []) {
            throw ValidationException::withMessages(['items' => 'Choose at least one coffee or pastry.']);
        }

        $pickupAt = Carbon::parse($data['pickup_date'].' '.$data['pickup_time']);
        $extraItems = $data['order_type'] === 'lunch'
            ? [['name' => $data['lunch_description'], 'quantity' => 1, 'unit_price' => 0, 'line_total' => 0]]
            : [];

        $order = $this->persistOrder($cart, [
            'order_type' => $data['order_type'],
            'customer_name' => Auth::user()->name,
            'pickup_at' => $pickupAt,
            'special_instructions' => $data['special_instructions'] ?? null,
            'status' => 'Pending',
        ], $extraItems);

        return redirect()->route('user.orders')->with('success', 'Order '.$order->reference.' has been placed.');
    }

    public function userOrders()
    {
        $this->requireCustomer();

        return view('issang.user.orders', [
            'orders' => Order::where('user_id', Auth::id())->latest()->paginate(8),
        ]);
    }

    public function notifications()
    {
        $isAdmin = $this->isAdmin();
        $items = $isAdmin
            ? [
                ['title' => 'New customer order', 'details' => 'An order is waiting for confirmation.', 'time' => 'Just now', 'tone' => 'pink'],
                ['title' => 'Inventory running low', 'details' => 'Strawberry Cookie has 5 items remaining.', 'time' => '12 minutes ago', 'tone' => 'gold'],
                ['title' => 'Daily sales report ready', 'details' => 'Your report for today is available.', 'time' => '1 hour ago', 'tone' => 'green'],
            ]
            : [
                ['title' => 'Your order is being prepared', 'details' => 'We will have your order ready for pickup.', 'time' => 'Just now', 'tone' => 'pink'],
                ['title' => 'Thanks for stopping by', 'details' => 'Your latest order is in your order history.', 'time' => 'Yesterday', 'tone' => 'gold'],
            ];

        return view('issang.notifications', compact('items', 'isAdmin'));
    }

    public function adminDashboard()
    {
        $this->requireAdmin();
        $todayOrders = Order::whereDate('created_at', today());

        return view('issang.admin.dashboard', [
            'todayOrders' => (clone $todayOrders)->count(),
            'todaySales' => (clone $todayOrders)->where('status', 'Completed')->sum('total'),
            'customerCount' => User::where('role', 'user')->count(),
            'lowStockCount' => Product::where('is_active', true)->where('stock', '<=', 5)->count(),
            'onlineOrders' => Order::whereNotNull('user_id')->latest()->take(5)->get(),
            'walkInSales' => Order::whereNull('user_id')->latest()->take(5)->get(),
            'lowStockProducts' => Product::where('is_active', true)->where('stock', '<=', 5)->orderBy('stock')->take(5)->get(),
        ]);
    }

    public function adminOrders(Request $request)
    {
        $this->requireAdmin();
        $query = Order::latest();

        if ($request->filled('status') && $request->input('status') !== 'All Orders') {
            $query->where('status', $request->input('status'));
        }

        return view('issang.admin.orders', [
            'orders' => $query->paginate(10)->withQueryString(),
            'status' => $request->input('status', 'All Orders'),
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $this->requireAdmin();
        $data = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'Confirmed', 'Preparing', 'Ready to pick up', 'Completed', 'Cancelled'])],
        ]);
        $order->update($data);

        return back()->with('success', 'Order status updated.');
    }

    public function sales()
    {
        $this->requireAdmin();

        return view('issang.admin.sales', [
            'products' => Product::where('is_active', true)->where('stock', '>', 0)->orderBy('category')->orderBy('name')->get(),
            'salesToday' => Order::whereNull('user_id')->whereDate('created_at', today())->where('status', 'Completed')->sum('total'),
        ]);
    }

    public function storeSale(Request $request)
    {
        $this->requireAdmin();
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in(['Cash', 'GCash'])],
            'items' => ['required', 'json'],
        ]);
        $cart = $this->decodeCart($data['items']);
        if ($cart === []) {
            throw ValidationException::withMessages(['items' => 'Add at least one item to the current order.']);
        }

        $order = $this->persistOrder($cart, [
            'order_type' => 'walk-in',
            'user_id' => null,
            'customer_name' => $data['customer_name'] ?? 'Walk-in customer',
            'payment_method' => $data['payment_method'],
            'status' => 'Completed',
        ]);

        return redirect()->route('admin.sales')->with('success', 'Sale '.$order->reference.' completed.');
    }

    public function inventory(Request $request)
    {
        $this->requireAdmin();
        $query = Product::query()->orderBy('category')->orderBy('name');

        if ($request->filled('category') && $request->input('category') !== 'All Types') {
            $query->where('category', strtolower($request->input('category')));
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->input('search').'%');
        }

        return view('issang.admin.inventory', [
            'products' => $query->paginate(10)->withQueryString(),
            'categories' => Product::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
            'category' => $request->input('category', 'All Types'),
            'search' => $request->input('search', ''),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $this->requireAdmin();
        Product::create($this->validatedProduct($request));

        return redirect()->route('admin.inventory')->with('success', 'Product added to inventory.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $this->requireAdmin();
        $product->update($this->validatedProduct($request));

        return redirect()->route('admin.inventory')->with('success', 'Product details updated.');
    }

    public function deleteProduct(Product $product)
    {
        $this->requireAdmin();
        $product->update(['is_active' => false]);

        return redirect()->route('admin.inventory')->with('success', 'Product removed from the active menu.');
    }

    public function reports(Request $request)
    {
        $this->requireAdmin();
        $start = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay();
        $end = Carbon::parse($request->input('end_date', now()->toDateString()))->endOfDay();
        $orders = Order::whereBetween('created_at', [$start, $end])->where('status', 'Completed')->latest()->get();
        $topProducts = collect($orders->pluck('items')->flatten(1))
            ->groupBy('name')
            ->map(fn ($items, $name) => ['name' => $name, 'quantity' => $items->sum('quantity'), 'revenue' => $items->sum('line_total')])
            ->sortByDesc('quantity')
            ->take(5)
            ->values();

        return view('issang.admin.reports', [
            'orders' => $orders,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'revenue' => $orders->sum('total'),
            'quantity' => $orders->sum(fn (Order $order) => collect($order->items)->sum('quantity')),
            'topProducts' => $topProducts,
            'cashTotal' => $orders->where('payment_method', 'Cash')->sum('total'),
            'gcashTotal' => $orders->where('payment_method', 'GCash')->sum('total'),
        ]);
    }

    public function exportReports(Request $request)
    {
        $this->requireAdmin();
        $orders = Order::whereBetween('created_at', [
            Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay(),
            Carbon::parse($request->input('end_date', now()->toDateString()))->endOfDay(),
        ])->latest()->get();

        return response()->streamDownload(function () use ($orders) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Transaction', 'Date', 'Customer', 'Type', 'Payment', 'Status', 'Total']);
            foreach ($orders as $order) {
                fputcsv($output, [$order->reference, $order->created_at->toDateTimeString(), $order->customer_name, $order->order_type, $order->payment_method, $order->status, $order->total]);
            }
            fclose($output);
        }, 'issang-sales-report.csv');
    }

    private function persistOrder(array $cart, array $attributes, array $extraItems = []): Order
    {
        return DB::transaction(function () use ($cart, $attributes, $extraItems) {
            $lines = $extraItems;
            $subtotal = 0;

            foreach ($cart as $line) {
                $product = Product::where('is_active', true)->lockForUpdate()->find($line['product_id']);
                if (! $product || $product->stock < $line['quantity']) {
                    throw ValidationException::withMessages(['items' => 'One or more items are no longer available in that quantity.']);
                }

                $lineTotal = (float) $product->price * $line['quantity'];
                $subtotal += $lineTotal;
                $lines[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $line['quantity'],
                    'unit_price' => (float) $product->price,
                    'line_total' => round($lineTotal, 2),
                ];
                $product->decrement('stock', $line['quantity']);
            }

            $subtotal = round($subtotal, 2);
            $tax = 0;

            return Order::create($attributes + [
                'reference' => 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                'user_id' => Auth::id(),
                'items' => $lines,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);
        });
    }

    private function decodeCart(string $items): array
    {
        $decoded = json_decode($items, true);
        if (! is_array($decoded)) {
            throw ValidationException::withMessages(['items' => 'The selected items could not be read.']);
        }

        return collect($decoded)->map(function ($line) {
            return [
                'product_id' => filter_var($line['product_id'] ?? null, FILTER_VALIDATE_INT),
                'quantity' => filter_var($line['quantity'] ?? null, FILTER_VALIDATE_INT),
            ];
        })->filter(fn ($line) => $line['product_id'] && $line['quantity'] > 0 && $line['quantity'] <= 50)->values()->all();
    }

    private function validatedProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['lunch', 'coffee', 'pastry'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }

    private function isAdmin(): bool
    {
        return Auth::check() && in_array(strtolower(Auth::user()->role), ['admin', 'administrator'], true);
    }

    private function requireAdmin(): void
    {
        abort_unless($this->isAdmin(), 403);
    }

    private function requireCustomer(): void
    {
        abort_unless(Auth::check() && ! $this->isAdmin(), 403);
    }
}