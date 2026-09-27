@extends('layouts.app')
@section('title','Checkout')
@section('content')
  <h2 class="text-2xl bold mb-16">Checkout</h2>

  <form method="POST" action="{{ route('customer.checkout.store') }}">
    @csrf
    <div class="row" style="gap:24px;align-items:flex-start">
      <div class="card" style="flex:1;min-width:300px">
        <div class="card-header">Pickup Details</div>
        <div class="card-body">
          <div class="row">
            <div class="form-group" style="flex:1">
              <label class="form-label">Pickup Date</label>
              <input type="date" name="pickup_date" class="form-control" min="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group" style="flex:1">
              <label class="form-label">Pickup Time</label>
              <input type="time" name="pickup_time" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-control" required>
              <option value="cash">Cash</option>
              <option value="gcash">GCash</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Notes (optional)</label>
            <textarea name="notes" class="form-control" placeholder="Any special requests?"></textarea>
          </div>
        </div>
      </div>

      <div class="card" style="width:340px">
        <div class="card-header">Your Order</div>
        <div class="card-body">
          @foreach($cart->items as $item)
            <div class="flex between mb-16 text-sm">
              <span>{{ $item->quantity }}× {{ $item->product->name }}</span>
              <span class="bold">₱{{ number_format($item->product->price * $item->quantity,2) }}</span>
            </div>
          @endforeach
          <hr style="border:none;border-top:1px solid var(--border);margin:14px 0">
          <div class="flex between mb-16"><span class="bold">Total</span><span class="bold text-lg" style="color:var(--brand)">₱{{ number_format($subtotal,2) }}</span></div>
          <button class="btn btn-primary btn-block">Place Order</button>
          <p class="text-sm text-muted mt-16" style="text-align:center">Payment is made upon pickup.</p>
        </div>
      </div>
    </div>
  </form>
@endsection