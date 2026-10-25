@extends('layouts.app')
@section('title','Issang Macchiatos · Order Ahead, Skip the Line')

@section('content')

  {{-- HERO --}}
  <section class="hero">
    <div class="hero-inner">
      <h2>Skip the line.<br>Your coffee, ready when you are.</h2>
      <p>Freshly brewed coffee and oven-baked pastries — pre-order online and pick up at the counter, no waiting.</p>
      <div class="flex gap-12 hero-actions">
        <a href="{{ route('menu') }}" class="btn btn-primary">Browse Menu →</a>
      </div>
    </div>
  </section>

  {{-- WHY US --}}
  <div class="grid grid-4 mb-24">
    <div class="card card-body feature-card">
      <div class="feature-icon">⚡</div>
      <div class="feature-title">Skip the Queue</div>
      <p class="text-sm text-muted">Order ahead, pick up when ready.</p>
    </div>
    <div class="card card-body feature-card">
      <div class="feature-icon">☕</div>
      <div class="feature-title">Fresh Brews</div>
      <p class="text-sm text-muted">Made-to-order every time.</p>
    </div>
    <div class="card card-body feature-card">
      <div class="feature-icon">🥐</div>
      <div class="feature-title">Daily Pastries</div>
      <p class="text-sm text-muted">Baked fresh every morning.</p>
    </div>
    <div class="card card-body feature-card">
      <div class="feature-icon">💳</div>
      <div class="feature-title">Pay at Pickup</div>
      <p class="text-sm text-muted">Cash or GCash — no online payment.</p>
    </div>
  </div>

  {{-- HOW IT WORKS --}}
  <section class="mb-24">
    <h3 class="text-xl bold section-title-center">How It Works</h3>
    <p class="text-muted text-sm section-sub-center">Three steps to your perfect cup.</p>
    <div class="grid grid-3">
      <div class="card card-body step-card">
        <div class="step-icon">🍽️</div>
        <div class="bold text-lg step-title">1. Browse</div>
        <p class="text-sm text-muted mt-8">Explore our menu of coffee, drinks, and pastries.</p>
      </div>
      <div class="card card-body step-card">
        <div class="step-icon">🛒</div>
        <div class="bold text-lg step-title">2. Pre-Order</div>
        <p class="text-sm text-muted mt-8">Pick a pickup date and time. Checkout in one click.</p>
      </div>
      <div class="card card-body step-card">
        <div class="step-icon">☕</div>
        <div class="bold text-lg step-title">3. Pick Up</div>
        <p class="text-sm text-muted mt-8">Walk in, grab your order, and go.</p>
      </div>
    </div>
  </section>

  {{-- MENU PREVIEW --}}
  <div class="flex between mb-16">
    <h3 class="text-xl bold">Featured Drinks</h3>
    <a href="{{ route('menu') }}" class="text-sm" style="color:var(--brand)">View full menu →</a>
  </div>
  <div class="grid grid-4 mb-24">
    @foreach($featured as $p)
      @include('components.product-card', ['product' => $p])
    @endforeach
  </div>

  <div class="flex between mb-16">
    <h3 class="text-xl bold">Fresh Pastries</h3>
  </div>
  <div class="grid grid-4 mb-24">
    @foreach($pastries as $p)
      @include('components.product-card', ['product' => $p])
    @endforeach
  </div>

  {{-- ABOUT --}}
  <section class="grid grid-2 mb-24">
    <div class="card card-body about-card">
      <h4 class="text-lg bold mb-16">About Issang Macchiatos</h4>
      <p>{{ $about['story'] }}</p>
    </div>
    <div class="card card-body about-card">
      <h4 class="text-lg bold mb-16">Visit Us</h4>
      <p>📍 {{ $about['address'] }}</p>
      <p class="about-line">📞 {{ $about['phone'] }}</p>
      <p class="about-line">🕒 {{ $about['hours'] }}</p>
    </div>
  </section>
        
        @guest('customer')
          <a href="{{ route('customer.guest.checkout') }}" class="btn btn-hero-outline">
            Order as Guest
          </a>
        @endguest
      </div>

@endsection