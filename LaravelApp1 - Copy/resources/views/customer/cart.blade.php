@extends('layouts.app')
@section('title','My Cart')
@section('content')
  <h2 class="text-2xl bold mb-16">My Cart</h2>

  @if(($cart->items ?? collect())->isEmpty())
    <div class="card card-body" style="text-align:center;padding:48px">
      <div style="font-size:3rem">🛒</div>
      <p class="text-muted mt-16">Your cart is empty.</p>
      <a href="{{ route('menu') }}" class="btn btn-primary mt-16">Browse Menu</a>
    </div>
  @else
    <div class="row" style="gap:24px;align-items:flex-start">
      <div class="card" style="flex:1;min-width:300px">
        <div class="card-header">Items ({{ $cart->items->count() }})</div>
        <div class="card-body">
          @foreach($cart->items as $item)
            <div class="cart-item">
              @if(!empty($item->image_url))
  <img src="{{ asset($item->image_url) }}" alt="{{ $item->product->name }}" style="width:70px;height:70px;border-radius:10px;object-fit:cover;flex-shrink:0;">
@else
  <div class="cart-thumb">☕</div>
@endif
              <div style="flex:1">
                <div class="bold">{{ $item->product->name }}</div>
                <div class="text-sm text-muted">{{ $item->special_instructions }}</div>
                <div class="text-sm" style="color:var(--brand);font-weight:700">₱{{ number_format($item->product->price,2) }}</div>
              </div>
              <form method="POST" action="{{ route('customer.cart.update', $item->id) }}" data-cart-update>
                @csrf @method('PUT')
                <div class="qty">
                  <button type="button" data-dec>−</button>
                  <input type="number" name="quantity" value="{{ $item->quantity }}" min="1">
                  <button type="button" data-inc>+</button>
                </div>
              </form>
              <form method="POST" action="{{ route('customer.cart.remove', $item->id) }}">
                @csrf @method('DELETE')
                <button class="btn btn-outline btn-sm" style="color:var(--danger)">Remove</button>
              </form>
            </div>
          @endforeach
        </div>
      </div>

      <div class="card" style="width:320px">
        <div class="card-header">Order Summary</div>
        <div class="card-body">
          <div class="flex between mb-16"><span>Subtotal</span><span class="bold">₱{{ number_format($subtotal,2) }}</span></div>
          <div class="flex between mb-16"><span>Total</span><span class="bold text-lg" style="color:var(--brand)">₱{{ number_format($subtotal,2) }}</span></div>
          <a href="{{ route('customer.checkout') }}" class="btn btn-primary btn-block">Proceed to Checkout →</a>
        </div>
      </div>
    </div>
  @endif
@endsection