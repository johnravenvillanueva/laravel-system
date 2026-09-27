@extends('layouts.issang')

@section('title', 'Admin Dashboard | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Issang Macchiatos · Operations</p><h1>Welcome, Administrator</h1><p class="page-subtitle">A quick look at today's café activity.</p></div>
        <a class="secondary-button" href="{{ route('admin.reports') }}">View sales report <span aria-hidden="true">→</span></a>
    </div>

    <section class="stats-grid" aria-label="Today's summary">
        <article class="stat-card"><span class="stat-icon stat-icon-pink" aria-hidden="true">▤</span><span class="stat-label">Orders Today</span><strong>{{ $todayOrders }}</strong><small>All order types</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon-gold" aria-hidden="true">₱</span><span class="stat-label">Today's Sales</span><strong>₱{{ number_format($todaySales, 2) }}</strong><small>Completed transactions</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon-green" aria-hidden="true">♙</span><span class="stat-label">Total Customers</span><strong>{{ number_format($customerCount) }}</strong><small>Registered users</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon-red" aria-hidden="true">!</span><span class="stat-label">Low Stock Items</span><strong>{{ $lowStockCount }}</strong><small>5 units or fewer</small></article>
    </section>

    <div class="dashboard-columns">
        <section class="data-section">
            <div class="section-heading-row"><div><p class="eyebrow">Customer pickup orders</p><h2>Recent Online Orders</h2></div><a class="text-link" href="{{ route('admin.orders') }}">All orders <span aria-hidden="true">→</span></a></div>
            @if ($onlineOrders->isEmpty())
                <div class="empty-inline">No customer orders yet.</div>
            @else
                <div class="table-scroll"><table class="data-table"><thead><tr><th>Order #</th><th>Customer name</th><th>Type</th><th>Status</th><th class="align-right">Amount</th></tr></thead><tbody>
                    @foreach ($onlineOrders as $order)
                        <tr><td class="mono">{{ $order->reference }}</td><td>{{ $order->customer_name ?? $order->user?->name }}</td><td>{{ Str::headline($order->order_type) }}</td><td><span class="status-badge status-{{ Str::slug($order->status) }}">{{ $order->status }}</span></td><td class="align-right">₱{{ number_format($order->total, 2) }}</td></tr>
                    @endforeach
                </tbody></table></div>
            @endif
        </section>

        <section class="data-section">
            <div class="section-heading-row"><div><p class="eyebrow">Counter transactions</p><h2>Recent Walk-in Sales</h2></div><a class="text-link" href="{{ route('admin.sales') }}">New sale <span aria-hidden="true">→</span></a></div>
            @if ($walkInSales->isEmpty())
                <div class="empty-inline">No walk-in transactions yet.</div>
            @else
                <div class="table-scroll"><table class="data-table"><thead><tr><th>Transaction #</th><th>Customer</th><th>Time</th><th class="align-right">Amount</th></tr></thead><tbody>
                    @foreach ($walkInSales as $order)
                        <tr><td class="mono">{{ $order->reference }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->created_at->format('h:i A') }}</td><td class="align-right">₱{{ number_format($order->total, 2) }}</td></tr>
                    @endforeach
                </tbody></table></div>
            @endif
        </section>
    </div>

    <section class="data-section low-stock-section">
        <div class="section-heading-row"><div><p class="eyebrow">Inventory attention</p><h2>Items Needing Restocking</h2></div><a class="text-link" href="{{ route('admin.inventory', ['category' => 'All Types']) }}">Open inventory <span aria-hidden="true">→</span></a></div>
        @if ($lowStockProducts->isEmpty())
            <div class="empty-inline">All active products have more than five units in stock.</div>
        @else
            <div class="low-stock-list">
                @foreach ($lowStockProducts as $product)
                    <div class="low-stock-row"><span class="stock-dot {{ $product->stock === 0 ? 'is-out' : '' }}" aria-hidden="true"></span><strong>{{ $product->name }}</strong><span>{{ Str::headline($product->category) }}</span><span class="stock-count">{{ $product->stock }} left</span><a href="{{ route('admin.inventory', ['search' => $product->name]) }}">Update stock</a></div>
                @endforeach
            </div>
        @endif
    </section>
@endsection