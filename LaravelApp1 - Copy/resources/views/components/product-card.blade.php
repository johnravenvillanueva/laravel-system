<div class="card product-card">
  <a href="{{ route('customer.product.show', $product->id) }}">
    <div class="product-img">☕</div>
  </a>
  <div class="product-body">
    <a href="{{ route('customer.product.show', $product->id) }}" class="bold">{{ $product->name }}</a>
    <div class="text-sm text-muted">{{ Str::limit($product->description, 50) }}</div>
    <div class="flex between center mt-8">
      <span class="product-price">₱{{ number_format($product->price,2) }}</span>
      <form method="POST" action="{{ route('customer.cart.add', $product->id) }}">
        @csrf
        <input type="hidden" name="quantity" value="1">
        <button class="btn btn-primary btn-sm">Add</button>
      </form>
    </div>
  </div>
</div>