@extends('layouts.app')
@section('title','Order Details')
@section('content')
  <a href="{{ route('customer.orders') }}" class="text-sm" style="color:var(--brand)">← Back to orders</a>
  <h2 class="text-2xl bold mt-16 mb-16">Order #{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}</h2>

  <div class="row" style="gap:24px;align-items:flex-start">
    <div class="card" style="flex:1;min-width:300px">
      <div class="card-header">Items</div>
      <div class="card-body">
        @foreach($order->items as $item)
          <div class="flex between mb-16">
            <div>
              <div class="bold">{{ $item->product->name }}</div>
              <div class="text-sm text-muted">Qty: {{ $item->quantity }} · ₱{{ number_format($item->unit_price,2) }}</div>
              @if($item->special_instructions)<div class="text-sm text-muted">Note: {{ $item->special_instructions }}</div>@endif
            </div>
            <div class="bold">₱{{ number_format($item->subtotal,2) }}</div>
          </div>
        @endforeach
        <hr style="border:none;border-top:1px solid var(--border);margin:14px 0">
        <div class="flex between"><span class="bold">Total</span><span class="bold text-lg" style="color:var(--brand)">₱{{ number_format($order->total_amount,2) }}</span></div>
      </div>
    </div>

    <div class="card" style="width:320px">
      <div class="card-header">Status</div>
      <div class="card-body">
        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        <div class="mt-16 text-sm"><strong>Pickup:</strong> {{ $order->pickup_date }} {{ $order->pickup_time }}</div>
        <div class="mt-8 text-sm"><strong>Payment:</strong> {{ ucfirst($order->payment_method) }}</div>
        <div class="mt-8 text-sm"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</div>
        @if($order->notes)<div class="mt-8 text-sm"><strong>Notes:</strong> {{ $order->notes }}</div>@endif
      </div>
    </div>
  </div>
@endsection