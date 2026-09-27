@extends('layouts.app')
@section('title','My Orders')
@section('content')
  <h2 class="text-2xl bold mb-16">My Orders</h2>

  @forelse($orders ?? [] as $order)
    <div class="card mb-16">
      <div class="card-body flex between center" style="flex-wrap:wrap;gap:12px">
        <div>
          <div class="bold">Order #{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}</div>
          <div class="text-sm text-muted">{{ $order->created_at->format('M d, Y · h:i A') }}</div>
          <div class="text-sm text-muted">Pickup: {{ $order->pickup_date }} {{ $order->pickup_time }}</div>
        </div>
        <div style="text-align:right">
          <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
          <div class="bold mt-8" style="color:var(--brand)">₱{{ number_format($order->total_amount,2) }}</div>
        </div>
        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline btn-sm">View Details</a>
      </div>
    </div>
  @empty
    <div class="card card-body" style="text-align:center;padding:48px">
      <div style="font-size:3rem">📦</div>
      <p class="text-muted mt-16">You have no orders yet.</p>
      <a href="{{ route('menu') }}" class="btn btn-primary mt-16">Start Ordering</a>
    </div>
  @endforelse
@endsection