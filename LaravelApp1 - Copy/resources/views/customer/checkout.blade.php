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
          <div class="pickup-fields">
            <label class="pickup-field">
              <span class="pickup-field-heading">
                <span class="pickup-field-icon">@include('partials.icon', ['ico' => 'calendar'])</span>
                <span><strong>Pickup date</strong><small>Choose your day</small></span>
              </span>
              <input type="date" name="pickup_date" class="form-control pickup-control" min="{{ date('Y-m-d') }}" value="{{ old('pickup_date') }}" required>
            </label>
            <label class="pickup-field">
              <span class="pickup-field-heading">
                <span class="pickup-field-icon">@include('partials.icon', ['ico' => 'clock'])</span>
                <span><strong>Pickup time</strong><small>Choose a time</small></span>
              </span>
              <input type="time" name="pickup_time" class="form-control pickup-control" value="{{ old('pickup_time') }}" required>
              @error('pickup_time') <span class="pickup-error">{{ $message }}</span> @enderror
            </label>
          </div>
          <p class="pickup-hint">Each pickup slot allows up to 5 orders.</p>
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