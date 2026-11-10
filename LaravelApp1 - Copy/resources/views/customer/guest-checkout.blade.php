@extends('layouts.app')
@section('title','Guest Checkout')

@section('content')

  <div class="flash flash-info mb-16">
    🛍️ You're checking out as a <strong>guest</strong>. No account needed — we'll just need a name and contact number to reach you for pickup.
  </div>

  <h2 class="text-2xl bold mb-16">Guest Checkout</h2>
  @error('cart')
    <div class="flash flash-error mb-16">{{ $message }}</div>
  @enderror
  @if(collect($cartItems)->contains(fn ($item) => empty($item['is_orderable'])))
    <div class="flash flash-error mb-16">Some items in your cart are out of stock or no longer have enough ingredients. Remove them before ordering.</div>
  @endif

  <form method="POST" action="{{ route('customer.guest.place') }}">
    @csrf
    <div class="row guest-layout">

      <div class="card guest-col">
        <div class="card-header">Contact Details</div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name') }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Contact Number</label>
            <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone') }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email (optional)</label>
            <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email') }}">
          </div>
        </div>
      </div>

      <div class="card guest-col">
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
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" placeholder="Anything we should know?"></textarea>
          </div>
        </div>
      </div>

      <div class="card guest-summary">
        <div class="card-header">Order Summary</div>
        <div class="card-body">
          @if(!empty($cartItems))
            @foreach($cartItems as $pid => $item)
              <div class="flex between mb-16 text-sm">
                <span>{{ $item['qty'] }}× {{ $item['name'] }} @if(empty($item['is_orderable']))<strong style="color:var(--danger)">(Out of stock)</strong>@endif</span>
                <span class="bold">₱{{ number_format($item['price'] * $item['qty'],2) }}
                  <button type="submit" form="remove-{{ $pid }}" class="icon-btn" aria-label="Remove {{ $item['name'] }}" title="Remove">✕</button>
                </span>
              </div>
            @endforeach
          @else
            <p class="text-sm text-muted">Your cart is empty.</p>
          @endif

          <hr>
          <div class="flex between mb-16">
            <span class="bold">Total</span>
            <span class="bold text-lg guest-summary-total">₱{{ number_format($subtotal,2) }}</span>
          </div>
          <button class="btn btn-primary btn-block" @disabled(empty($cartItems) || collect($cartItems)->contains(fn ($item) => empty($item['is_orderable'])))>Place Guest Order</button>
          <p class="text-sm text-muted guest-note">Payment is made upon pickup.</p>
        </div>
      </div>
    </div>
  </form>

  @foreach($cartItems as $pid => $item)
    <form id="remove-{{ $pid }}" method="POST" action="{{ route('customer.guest.cart.remove', $pid) }}" style="display:none">
      @csrf @method('DELETE')
    </form>
  @endforeach

  <div class="guest-footer">
    <p class="text-sm text-muted">Want to track your orders and re-order faster?</p>
    <a href="{{ route('register') }}" class="btn btn-outline">Create an Account</a>
  </div>

@endsection