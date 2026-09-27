@extends('layouts.app')
@section('title','Home · Issang Macchiatos')
@section('content')
  <section class="hero">
    <h2>Freshly brewed, freshly baked — every single day.</h2>
    <p>Pre-order your favorite coffee and pastries online, skip the line, and pick up when it's ready.</p>
    <a href="{{ route('menu') }}" class="btn btn-primary">Browse Menu →</a>
  </section>

  <div class="flex between mb-16">
    <h3 class="text-xl bold">Featured Drinks</h3>
    <a href="{{ route('menu') }}" class="text-sm" style="color:var(--brand)">View all →</a>
  </div>

  <div class="grid grid-4 mb-24">
    @foreach($featured ?? [] as $p)
      @include('components.product-card', ['product' => $p])
    @endforeach
  </div>

  <div class="flex between mb-16">
    <h3 class="text-xl bold">Fresh Pastries</h3>
  </div>
  <div class="grid grid-4">
    @foreach($pastries ?? [] as $p)
      @include('components.product-card', ['product' => $p])
    @endforeach
  </div>
@endsection