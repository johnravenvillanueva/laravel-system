@extends('layouts.admin')
@section('title','New Walk-in Sale')
@section('page-title','New Walk-in Sale')
@section('content')
  <form method="POST" action="{{ route('admin.sales.store') }}">
    @csrf
    <div class="row" style="gap:24px;align-items:flex-start">
      <div class="card" style="flex:2;min-width:320px">
        <div class="card-header">Select Products</div>
        <div class="card-body">
          <div class="search-box mb-16"><input type="text" id="productSearch" class="form-control" placeholder="Search product…"></div>
          <div class="grid grid-3" id="productGrid">
            @foreach($products ?? [] as $p)
              <div class="card product-card" data-name="{{ strtolower($p->name) }}" style="cursor:pointer"
                   onclick="addToSale({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }})">
                <div class="product-img" style="aspect-ratio:1/1">☕</div>
                <div class="product-body">
                  <div class="bold">{{ $p->name }}</div>
                  <div class="product-price">₱{{ number_format($p->price,2) }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="card" style="flex:1;min-width:300px">
        <div class="card-header">Current Sale</div>
        <div class="card-body">
          <div id="saleItems"></div>
          <hr style="border:none;border-top:1px solid var(--border);margin:16px 0">
          <div class="flex between mb-16"><span class="bold">Total</span><span class="bold text-lg" id="saleTotal" style="color:var(--brand)">₱0.00</span></div>

          <div class="form-group">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-control" required>
              <option value="cash">Cash</option>
              <option value="gcash">GCash</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
          </div>
          <input type="hidden" name="items" id="itemsInput">
          <button class="btn btn-primary btn-block" id="submitSale">Complete Sale</button>
        </div>
      </div>
    </div>
  </form>
@endsection

@push('scripts')
<script>
const saleItems = {};
function addToSale(id, name, price){
  if (saleItems[id]) saleItems[id].qty++;
  else saleItems[id] = { id, name, price, qty: 1 };
  renderSale();
}
function removeFromSale(id){ delete saleItems[id]; renderSale(); }
function changeQty(id, delta){
  if(!saleItems[id]) return;
  saleItems[id].qty += delta;
  if (saleItems[id].qty <= 0) delete saleItems[id];
  renderSale();
}
function renderSale(){
  const wrap = document.getElementById('saleItems');
  const totalEl = document.getElementById('saleTotal');
  let total = 0, html = '';
  Object.values(saleItems).forEach(i=>{
    total += i.price * i.qty;
    html += `<div class="flex between mb-16 center">
      <div><div class="bold">${i.name}</div><div class="text-sm text-muted">₱${i.price.toFixed(2)}</div></div>
      <div class="qty">
        <button type="button" onclick="changeQty(${i.id},-1)">−</button>
        <input value="${i.qty}" readonly>
        <button type="button" onclick="changeQty(${i.id},1)">+</button>
      </div>
      <button type="button" class="btn btn-outline btn-sm" style="color:var(--danger)" onclick="removeFromSale(${i.id})">✕</button>
    </div>`;
  });
  wrap.innerHTML = html || '<p class="text-muted text-sm">Click a product to add it.</p>';
  totalEl.textContent = '₱' + total.toFixed(2);
  document.getElementById('itemsInput').value = JSON.stringify(Object.values(saleItems));
}
document.getElementById('productSearch')?.addEventListener('input', e=>{
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('#productGrid .product-card').forEach(c=>{
    c.style.display = c.dataset.name.includes(q) ? '' : 'none';
  });
});
</script>
@endpush