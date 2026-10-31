<div class="card product-card">
  <a href="{{ route('customer.product.show', $product->product_id) }}" class="product-media" tabindex="-1" aria-hidden="true">
    @if($product->image_url)
      <img src="{{ asset($product->image_url) }}" alt="" loading="lazy" width="400" height="400">
    @else
      <div class="product-placeholder">No image</div>
    @endif
  </a>
  <div class="product-body">
    <a href="{{ route('customer.product.show', $product->product_id) }}" class="product-name">{{ $product->name }}</a>
    <div class="text-sm text-muted">{{ Str::limit($product->description, 56) }}</div>
    <div class="flex between mt-8">
      <span class="product-price">₱{{ number_format($product->price, 2) }}</span>
      <form method="POST" action="{{ route('customer.cart.add', $product->product_id) }}">
        @csrf
        <input type="hidden" name="quantity" value="1">
        <button class="btn btn-primary btn-sm" aria-label="Add {{ $product->name }} to cart">@include('partials.icon', ['ico' => 'plus', 'icoClass' => 'icon-sm']) Add</button>
      </form>
    </div>
  </div>
</div>
