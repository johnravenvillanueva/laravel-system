@extends('layouts.issang')

@section('title', 'Customer Dashboard | Issang Macchiatos')

@section('content')
    <section class="customer-hero">
        <div>
            <p class="eyebrow">Your neighborhood café</p>
            <h1>Welcome to Issang Macchiatos, {{ Auth::user()->name }}!</h1>
            <p>Ready to order your favorite coffee, pastry or lunch? Pick your favorites and choose a pickup time.</p>
            <a class="primary-button" href="{{ route('user.order.create') }}">Place an Order <span aria-hidden="true">→</span></a>
        </div>
        <div class="hero-seal" aria-hidden="true"><span>IM</span><small>MADE FRESH<br>EVERY DAY</small></div>
    </section>

    <div class="customer-shortcuts">
        <a class="shortcut-item" href="{{ route('user.order.create') }}">
            <span class="shortcut-icon" aria-hidden="true">☕</span>
            <span><strong>Place an order</strong><small>Order pastry, coffee or lunch</small></span>
            <span class="shortcut-arrow" aria-hidden="true">→</span>
        </a>
        <a class="shortcut-item" href="{{ route('user.orders') }}">
            <span class="shortcut-icon shortcut-icon-pink" aria-hidden="true">▤</span>
            <span><strong>My orders</strong><small>{{ $orderCount }} order{{ $orderCount === 1 ? '' : 's' }} in your history</small></span>
            <span class="shortcut-arrow" aria-hidden="true">→</span>
        </a>
        <a class="shortcut-item" href="{{ route('notifications.index') }}">
            <span class="shortcut-icon shortcut-icon-gold" aria-hidden="true">♧</span>
            <span><strong>Notifications</strong><small>Pickup updates and café news</small></span>
            <span class="shortcut-arrow" aria-hidden="true">→</span>
        </a>
    </div>

    <section class="content-section">
        <div class="section-heading-row">
            <div>
                <p class="eyebrow">Keep an eye on your pickup</p>
                <h2>Upcoming Orders</h2>
            </div>
            <a class="text-link" href="{{ route('user.orders') }}">View order history <span aria-hidden="true">→</span></a>
        </div>

        @if ($orders->isEmpty())
            <div class="empty-state">
                <span class="empty-mark" aria-hidden="true">☕</span>
                <div><strong>No upcoming orders yet</strong><p>Your next café favorite is just around the corner.</p></div>
                <a class="secondary-button" href="{{ route('user.order.create') }}">Browse the menu</a>
            </div>
        @else
            <div class="order-card-list">
                @foreach ($orders as $order)
                    <article class="customer-order-card">
                        <div class="order-card-topline">
                            <span class="order-reference">Order # {{ $order->reference }}</span>
                            <span class="status-badge status-{{ Str::slug($order->status) }}">{{ $order->status }}</span>
                        </div>
                        <div class="order-card-summary">
                            <div><span class="summary-label">Pickup</span><strong>{{ $order->pickup_at?->format('M j, Y · h:i A') ?? 'Walk-in' }}</strong></div>
                            <div><span class="summary-label">Type</span><strong>{{ Str::headline($order->order_type) }}</strong></div>
                            <div><span class="summary-label">Amount</span><strong>₱{{ number_format($order->total, 2) }}</strong></div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection