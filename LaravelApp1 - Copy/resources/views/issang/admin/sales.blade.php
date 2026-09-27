@extends('layouts.issang')

@section('title', 'Walk-in Sales | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Point of sale</p><h1>Walk-in Sales</h1><p class="page-subtitle">Add items, choose a payment type and complete the transaction.</p></div>
        <div class="today-sales">Today's sales <strong>₱{{ number_format($salesToday, 2) }}</strong></div>
    </div>

    <div class="sales-layout">
        <section class="sales-catalog">
            <div class="sales-toolbar">
                <label class="search-field"><span class="visually-hidden">Search products</span><input type="search" id="sales-search" placeholder="Search products"></label>
                <div class="filter-tabs" role="tablist" aria-label="Product category">
                    <button type="button" class="filter-tab is-selected" data-filter="all">All Types</button>
                    <button type="button" class="filter-tab" data-filter="coffee">Coffee</button>
                    <button type="button" class="filter-tab" data-filter="pastry">Pastry</button>
                    <button type="button" class="filter-tab" data-filter="lunch">Lunch</button>
                </div>
            </div>
            <div class="sales-product-grid" id="sales-products">
                @foreach ($products as $product)
                    <button class="sales-product" type="button" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-category="{{ $product->category }}" data-search="{{ strtolower($product->name) }}">
                        <span class="sales-product-image">
                            @if ($product->image)<img src="{{ $product->image }}" alt="" loading="lazy">@else<span aria-hidden="true">{{ $product->category === 'coffee' ? '☕' : ($product->category === 'lunch' ? '🍽️' : '🥐') }}</span>@endif
                            <span class="sales-product-add" aria-hidden="true">+</span>
                        </span>
                        <span class="sales-product-copy"><strong>{{ $product->name }}</strong><small>{{ Str::headline($product->category) }} · {{ $product->stock }} in stock</small><b>₱{{ number_format($product->price, 2) }}</b></span>
                    </button>
                @endforeach
            </div>
        </section>

        <aside class="current-order panel">
            <div class="panel-heading"><div><p class="panel-kicker">Counter checkout</p><h2>Current Order</h2></div><button class="text-button" type="button" id="clear-sale" hidden>Clear</button></div>
            <form method="POST" action="{{ route('admin.sales.store') }}" id="sale-form">
                @csrf
                <input type="hidden" name="items" id="sale-cart-payload" value="[]">
                <div class="sale-cart-items" id="sale-cart-items"><p class="empty-cart">Add a product to start this sale.</p></div>
                <div class="sale-total"><span>Subtotal</span><strong id="sale-subtotal">₱0.00</strong></div>
                <div class="sale-total sale-grand-total"><span>Total</span><strong id="sale-total">₱0.00</strong></div>
                <label class="form-field"><span>Customer Name</span><input type="text" name="customer_name" placeholder="Enter customer name"></label>
                <label class="form-field"><span>Payment Type</span><select name="payment_method" required><option value="Cash">Cash</option><option value="GCash">GCash</option></select></label>
                @error('items')<p class="field-error">{{ $message }}</p>@enderror
                <button class="primary-button complete-sale-button" type="submit">Complete Sale <span aria-hidden="true">→</span></button>
            </form>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const cart = new Map();
    const money = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
    const list = document.getElementById('sale-cart-items');
    const payload = document.getElementById('sale-cart-payload');
    const clear = document.getElementById('clear-sale');

    function draw() {
        list.replaceChildren();
        let total = 0;
        if (!cart.size) {
            const empty = document.createElement('p');
            empty.className = 'empty-cart';
            empty.textContent = 'Add a product to start this sale.';
            list.append(empty);
        }
        cart.forEach((item, id) => {
            total += item.price * item.quantity;
            const row = document.createElement('div');
            row.className = 'sale-cart-row';
            const copy = document.createElement('div');
            copy.className = 'selected-item-copy';
            const name = document.createElement('strong');
            name.textContent = item.name;
            const amount = document.createElement('span');
            amount.textContent = money.format(item.price * item.quantity);
            copy.append(name, amount);
            const qty = document.createElement('div');
            qty.className = 'quantity-control';
            const minus = document.createElement('button');
            minus.type = 'button';
            minus.textContent = '−';
            minus.setAttribute('aria-label', `Remove one ${item.name}`);
            minus.addEventListener('click', () => change(id, -1));
            const number = document.createElement('span');
            number.textContent = item.quantity;
            const plus = document.createElement('button');
            plus.type = 'button';
            plus.textContent = '+';
            plus.setAttribute('aria-label', `Add one ${item.name}`);
            plus.addEventListener('click', () => change(id, 1));
            qty.append(minus, number, plus);
            row.append(copy, qty);
            list.append(row);
        });
        document.getElementById('sale-subtotal').textContent = money.format(total);
        document.getElementById('sale-total').textContent = money.format(total);
        payload.value = JSON.stringify([...cart.entries()].map(([product_id, item]) => ({ product_id: Number(product_id), quantity: item.quantity })));
        clear.hidden = !cart.size;
        document.querySelectorAll('.sales-product').forEach((card) => {
            const item = cart.get(card.dataset.productId);
            card.classList.toggle('is-added', Boolean(item));
            card.querySelector('.sales-product-add').textContent = item ? item.quantity : '+';
        });
    }

    function change(id, amount) {
        const item = cart.get(String(id));
        if (!item && amount > 0) {
            const card = [...document.querySelectorAll('.sales-product')].find((product) => product.dataset.productId === String(id));
            cart.set(String(id), { name: card.dataset.productName, price: Number(card.dataset.productPrice), quantity: 1 });
        } else if (item) {
            item.quantity += amount;
            if (item.quantity < 1) cart.delete(String(id));
        }
        draw();
    }

    document.querySelectorAll('.sales-product').forEach((card) => card.addEventListener('click', () => change(card.dataset.productId, 1)));
    clear.addEventListener('click', () => { cart.clear(); draw(); });
    document.getElementById('sale-form').addEventListener('submit', (event) => {
        if (!cart.size) {
            event.preventDefault();
            list.querySelector('.empty-cart')?.classList.add('has-error');
        }
    });
    document.getElementById('sales-search').addEventListener('input', (event) => {
        const query = event.target.value.toLowerCase().trim();
        document.querySelectorAll('.sales-product').forEach((card) => card.hidden = !card.dataset.search.includes(query));
    });
    document.querySelectorAll('.filter-tab').forEach((tab) => tab.addEventListener('click', () => {
        document.querySelectorAll('.filter-tab').forEach((item) => item.classList.toggle('is-selected', item === tab));
        document.querySelectorAll('.sales-product').forEach((card) => {
            card.hidden = (tab.dataset.filter !== 'all' && card.dataset.productCategory !== tab.dataset.filter) || !card.dataset.search.includes(document.getElementById('sales-search').value.toLowerCase().trim());
        });
    }));
    draw();
})();
</script>
@endpush