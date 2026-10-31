@extends('layouts.app')
@section('title','Issang Macchiatos · Order Ahead, Skip the Line')

@section('content')

  {{-- HERO --}}
  <section class="hero">
    <div class="hero-inner">
      <span class="hero-status">{{ $about['hours'] }}</span>
      <h1>Skip the line. Your coffee is ready when you are.</h1>
      <p>Freshly brewed coffee and oven-baked pastries. Pre-order online and pick up at the counter, no waiting.</p>
      <div class="hero-actions">
        <a href="{{ route('menu') }}" class="btn btn-primary">Browse menu @include('partials.icon', ['ico' => 'arrow-right'])</a>
        @guest('customer')
          <a href="{{ route('customer.guest.checkout') }}" class="btn btn-hero-outline">Order as guest</a>
        @endguest
      </div>
    </div>

    @php $heroPicks = $featured->filter(fn ($p) => $p->image_url)->take(3)->values(); @endphp
    @if($heroPicks->isNotEmpty())
      <div class="hero-media" aria-hidden="true">
        <span class="hero-ring"></span>
        @foreach($heroPicks as $i => $p)
          <figure class="hero-arch hero-arch-{{ $i + 1 }}">
            <img src="{{ asset($p->image_url) }}" alt="">
          </figure>
        @endforeach
      </div>
    @endif
  </section>

  {{-- WHY US --}}
  <section class="perks" aria-label="Why order with us">
    <div class="perk">
      <div class="perk-icon">@include('partials.icon', ['ico' => 'zap'])</div>
      <div><h3>Skip the queue</h3><p>Order ahead, pick up when it's ready.</p></div>
    </div>
    <div class="perk">
      <div class="perk-icon">@include('partials.icon', ['ico' => 'coffee'])</div>
      <div><h3>Fresh brews</h3><p>Made to order, every time.</p></div>
    </div>
    <div class="perk">
      <div class="perk-icon">@include('partials.icon', ['ico' => 'cookie'])</div>
      <div><h3>Daily pastries</h3><p>Baked fresh every morning.</p></div>
    </div>
    <div class="perk">
      <div class="perk-icon">@include('partials.icon', ['ico' => 'wallet'])</div>
      <div><h3>Pay at pickup</h3><p>Cash or GCash. Nothing to pay online.</p></div>
    </div>
  </section>

  {{-- HOW IT WORKS --}}
  <section class="section">
    <div class="section-head">
      <div>
        <h2>How it works</h2>
        <p>Three steps to your perfect cup.</p>
      </div>
    </div>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <h3>Browse</h3>
        <p>Explore our menu of coffee, drinks and pastries.</p>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <h3>Pre-order</h3>
        <p>Pick a pickup date and time, then check out in one go.</p>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <h3>Pick up</h3>
        <p>Walk in, grab your order and go.</p>
      </div>
    </div>
  </section>

  {{-- MENU PREVIEW --}}
  <section class="section">
    <div class="section-head">
      <h2>Featured drinks</h2>
      <a href="{{ route('menu') }}" class="link-arrow">View full menu @include('partials.icon', ['ico' => 'arrow-right'])</a>
    </div>
    <div class="grid grid-4">
      @foreach($featured as $p)
        @include('components.product-card', ['product' => $p])
      @endforeach
    </div>
  </section>

  @if($pastries->isNotEmpty())
    <section class="section">
      <div class="section-head">
        <h2>Fresh pastries</h2>
      </div>
      <div class="grid grid-4">
        @foreach($pastries as $p)
          @include('components.product-card', ['product' => $p])
        @endforeach
      </div>
    </section>
  @endif

  {{-- ABOUT --}}
  <section class="about-grid">
    <div class="card about-story">
      <h2>About Issang Macchiatos</h2>
      <p>{{ $about['story'] }}</p>
    </div>
    <div class="visit-card">
      <h2>Visit us</h2>
      <div class="visit-line">@include('partials.icon', ['ico' => 'map-pin']) <span>{{ $about['address'] }}</span></div>
      <div class="visit-line">@include('partials.icon', ['ico' => 'phone']) <span>{{ $about['phone'] }}</span></div>
      <div class="visit-line">@include('partials.icon', ['ico' => 'clock']) <span>{{ $about['hours'] }}</span></div>
    </div>
  </section>

@endsection
