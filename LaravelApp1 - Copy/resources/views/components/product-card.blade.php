<div class="card product-card">
  <a href="{{ route('customer.product.show', $product->product_id) }}">
    @if($product->image_url)
      <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}"
           style="aspect-ratio:1/1;width:100%;object-fit:cover;display:block">
    @else
      <div style="aspect-ratio:1/1; width:100%; background:#f7f4ef; display:flex; align-items:center; justify-content:center; color:#8a7a6d;">
        No Image
      </div>
    @endif
  </a>
  <div class="product-body">
    <a href="{{ route('customer.product.show', $product->product_id) }}" class="bold">{{ $product->name }}</a>
    <div class="text-sm text-muted">{{ Str::limit($product->description, 50) }}</div>
    <div class="flex between center mt-8">
      <span class="product-price">₱{{ number_format($product->price, 2) }}</span>
      <form method="POST" action="{{ route('customer.cart.add', $product->product_id) }}">
        @csrf
        <input type="hidden" name="quantity" value="1">
        <button class="btn btn-primary btn-sm">Add</button>
      </form>
    </div>
  </div>
</div>