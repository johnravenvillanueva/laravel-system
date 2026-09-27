@extends('layouts.issang')

@section('title', 'Orders | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Customer pickup queue</p><h1>Orders</h1><p class="page-subtitle">Review order details and keep pickup statuses current.</p></div>
        <form method="GET" action="{{ route('admin.orders') }}" class="inline-filter">
            <label class="visually-hidden" for="order-status-filter">Filter by status</label>
            <select class="filter-control" id="order-status-filter" name="status" onchange="this.form.submit()">
                @foreach (['All Orders', 'Pending', 'Confirmed', 'Preparing', 'Ready to pick up', 'Completed', 'Cancelled'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($orders->isEmpty())
        <div class="empty-state"><span class="empty-mark" aria-hidden="true">▤</span><div><strong>No orders found</strong><p>New customer orders will appear here.</p></div></div>
    @else
        <section class="admin-order-list">
            @foreach ($orders as $order)
                <article class="admin-order-row">
                    <div class="admin-order-main">
                        <div class="order-title-line"><strong>{{ $order->reference }}</strong><span class="status-badge status-{{ Str::slug($order->status) }}">{{ $order->status }}</span></div>
                        <span>{{ $order->customer_name ?? $order->user?->name ?? 'Walk-in customer' }} · {{ Str::headline($order->order_type) }}</span>
                        <small>Placed {{ $order->created_at->format('M j, Y · h:i A') }} · Pickup {{ $order->pickup_at?->format('M j · h:i A') ?? 'Walk-in' }}</small>
                    </div>
                    <div class="admin-order-items">
                        @foreach ($order->items as $item)
                            <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                        @endforeach
                    </div>
                    <strong class="admin-order-total">₱{{ number_format($order->total, 2) }}</strong>
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="status-update-form">
                        @csrf
                        @method('PATCH')
                        <label class="visually-hidden" for="status-{{ $order->id }}">Update {{ $order->reference }} status</label>
                        <select class="filter-control" id="status-{{ $order->id }}" name="status">
                            @foreach (['Pending', 'Confirmed', 'Preparing', 'Ready to pick up', 'Completed', 'Cancelled'] as $option)
                                <option @selected($order->status === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        <button class="quiet-button" type="submit">Update</button>
                    </form>
                </article>
            @endforeach
        </section>
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    @endif
@endsection