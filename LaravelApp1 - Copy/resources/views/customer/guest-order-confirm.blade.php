@extends('layouts.app')
@section('title','Order Placed')

@section('content')

  <div class="card card-body confirm-wrap">
    <div class="confirm-icon">✅</div>
    <h2 class="text-2xl bold mt-16">Order Placed!</h2>
    <p class="text-muted mt-8">
      Your guest order has been received. Please keep your order number and show it at the counter when you pick up.
    </p>

    <div class="card confirm-card">
      <div class="card-body">
        <div class="text-sm text-muted">Order Number</div>
        <div class="confirm-ref">{{ $order['ref'] }}</div>
        <div class="text-sm text-muted mt-16">Pickup</div>
        <div class="bold">{{ $order['pickup'] }}</div>
      </div>
    </div>

    <div class="flex gap-12 confirm-actions">
      <a href="{{ route('menu') }}" class="btn btn-primary">Order Again</a>
      <a href="{{ route('home') }}" class="btn btn-outline">Back to Home</a>
    </div>

    <p class="text-sm text-muted confirm-tip">
      💡 Want to track this order and re-order faster?
      <a href="{{ route('register') }}">Create a free account</a>.
    </p>
  </div>

@endsection