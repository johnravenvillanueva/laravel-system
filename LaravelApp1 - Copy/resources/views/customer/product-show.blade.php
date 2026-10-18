@extends('layouts.app')
@section('title', $product->name)
@section('content')
  <div class="row" style="gap:32px;align-items:flex-start">
    <div class="card" style="flex:1;min-width:280px;overflow:hidden">
  @if($product->image_url)
    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}"
         style="aspect-ratio:1/1;width:100%;object-fit:cover;display:block">
  @else
    <div class="product-img" style="aspect-ratio:1/1;font-size:5rem">☕</div>
  @endif
</div>
    <div style="flex:1;min-width:280px">
      <span class="badge badge-confirmed">{{ $product->category->name ?? 'Coffee' }}</span>
      <h2 class="text-2xl bold mt-16">{{ $product->name }}</h2>
      <p class="text-muted mt-8">{{ $product->description }}</p>
      <div class="text-2xl bold mt-16" style="color:var(--brand)">₱{{ number_format($product->price,2) }}</div>

      <form method="POST" action="{{ route('customer.cart.add', $product->id) }}" class="mt-24">
        @csrf
        <div class="form-group">
          <label class="form-label">Special Instructions</label>
          <textarea name="special_instructions" class="form-control" placeholder="e.g. less sugar, extra shot…"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Quantity</label>
          <div class="qty">
            <button type="button" data-dec>−</button>
            <input type="number" name="quantity" value="1" min="1">
            <button type="button" data-inc>+</button>
          </div>
        </div>
        <button class="btn btn-primary btn-block">Add to Cart 🛒</button>
      </form>
    </div>
  </div>
@endsection