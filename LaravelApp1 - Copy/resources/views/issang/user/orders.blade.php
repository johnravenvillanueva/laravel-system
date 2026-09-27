@extends('layouts.issang')

@section('title', 'My Orders | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Pickup updates and past favorites</p><h1>My Orders</h1><p class="page-subtitle">Your Issang order history, all in one place.</p></div>
        <a class="primary-button" href="{{ route('user.order.create') }}">Place an Order</a>
    </div>

    @if ($orders->isEmpty())
        <div class="empty-state"><span class="empty-mark" aria-hidden="true">▤</span><div><strong>No orders yet</strong><p>Your placed orders will show here with pickup details and status updates.</p></div><a class="secondary-button" href="{{ route('user.order.create') }}">Browse the menu</a></div>
    @else
        <div class="order-card-list">
            @foreach ($orders as $order)
                <article class="history-order panel">
                    <div class="order-card-topline">
                        <div><p class="panel-kicker">Placed {{ $order->created_at->format('M j, Y · h:i A') }}</p><h2>Order # {{ $order->reference }}</h2></div>
                        <span class="status-badge status-{{ Str::slug($order->status) }}">{{ $order->status }}</span>
                    </div>
                    <div class="order-card-summary">
                        <div><span class="summary-label">Pickup</span><strong>{{ $order->pickup_at?->format('M j, Y · h:i A') ?? 'Walk-in' }}</strong></div>
                        <div><span class="summary-label">Type</span><strong>{{ Str::headline($order->order_type) }}</strong></div>
                        <div><span class="summary-label">Amount</span><strong>₱{{ number_format($order->total, 2) }}</strong></div>
                    </div>
                    <details class="order-details">
                        <summary>See order details</summary>
                        <div class="order-detail-lines">
                            @foreach ($order->items as $item)
                                <div><span>{{ $item['name'] }} × {{ $item['quantity'] }}</span><strong>₱{{ number_format($item['line_total'], 2) }}</strong></div>
                            @endforeach
                        </div>
                        @if ($order->special_instructions)<p class="order-note"><strong>Special instructions:</strong> {{ $order->special_instructions }}</p>@endif
                    </details>
                </article>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    @endif
@endsection