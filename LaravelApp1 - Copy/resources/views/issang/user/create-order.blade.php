@extends('layouts.issang')

@section('title', 'Place an Order | Issang Macchiatos')

@section('content')
    <div class="page-heading order-page-heading">
        <div>
            <p class="eyebrow">Made fresh for your pickup</p>
            <h1>Place an Order</h1>
        </div>
        <a class="quiet-button" href="{{ route('user.orders') }}">My Orders</a>
    </div>

    <div class="order-type-tabs" role="tablist" aria-label="Order type">
        <button class="order-type-tab is-selected" type="button" role="tab" aria-selected="true" data-order-type="lunch">
            <span class="order-type-icon" aria-hidden="true">🍽️</span><span>Lunch Meal</span>
        </button>
        <button class="order-type-tab" type="button" role="tab" aria-selected="false" data-order-type="coffee">
            <span class="order-type-icon" aria-hidden="true">☕</span><span>Coffee and Pastry</span>
        </button>
    </div>

    <form class="customer-order-form" id="customer-order-form" method="POST" action="{{ route('user.orders.store') }}">
        @csrf
        <input type="hidden" name="order_type" id="order-type-value" value="lunch">
        <input type="hidden" name="items" id="cart-payload" value="[]">

        <section class="order-form-panel lunch-panel" id="lunch-panel" aria-labelledby="lunch-heading">
            <h2 id="lunch-heading">Lunch Details</h2>
            <label class="form-field meal-field">
                <span>What would you like for lunch?</span>
                <textarea name="lunch_description" rows="3" placeholder="Describe your lunch (ex. Chicken adobo with extra spice)"></textarea>
            </label>
        </section>

        <section class="menu-order-panel" id="coffee-panel" hidden>
            <div class="menu-category-tabs" role="tablist" aria-label="Menu category">
                <button class="menu-category-tab is-selected" type="button" data-category="coffee">Coffee</button>
                <button class="menu-category-tab" type="button" data-category="pastry">Pastry</button>
            </div>
            @foreach (['coffee' => 'Coffee Order', 'pastry' => 'Pastry Order'] as $category => $heading)
                <section class="menu-category" data-category-panel="{{ $category }}" @if ($category === 'pastry') hidden @endif>
                    <div class="section-heading-row compact-section-heading">
                        <div><p class="eyebrow">{{ $category === 'coffee' ? 'Espresso bar' : 'From the oven' }}</p><h2>{{ $heading }}</h2></div>
                        <span class="item-count">{{ $products->get($category, collect())->count() }} items</span>
                    </div>
                    <div class="menu-product-grid">
                        @foreach ($products->get($category, collect()) as $product)
                            <button class="menu-product-card" type="button" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}">
                                <span class="menu-product-image">
                                    @if ($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <span aria-hidden="true">{{ $category === 'coffee' ? '☕' : '🥐' }}</span>
                                    @endif
                                    <span class="product-add-mark" aria-hidden="true">+</span>
                                </span>
                                <span class="menu-product-copy"><strong>{{ $product->name }}</strong><small>{{ $product->description }}</small><b>₱{{ number_format($product->price, 2) }}</b></span>
                            </button>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </section>

        <section class="order-form-panel selected-order-panel" aria-labelledby="your-orders-heading">
            <div class="section-heading-row compact-section-heading">
                <div><p class="eyebrow">Your selection</p><h2 id="your-orders-heading">Your Orders <span id="cart-count">(0)</span></h2></div>
                <button class="text-button" id="clear-cart" type="button" hidden>Clear</button>
            </div>
            <div class="selected-items" id="selected-items"><p class="empty-cart">Your order is empty.</p></div>

            <fieldset class="pickup-fieldset">
                <legend>Pick-up Details</legend>
                <div class="form-field">
                    <label for="pickup-date">Pickup Date <span class="required-mark">*</span></label>
                    <input id="pickup-date" name="pickup_date" type="date" min="{{ now()->toDateString() }}" required>
                </div>
                <div class="form-field">
                    <label for="pickup-time">Pickup Time <span class="required-mark">*</span></label>
                    <input id="pickup-time" name="pickup_time" type="time" value="00:00" required>
                </div>
            </fieldset>

            <label class="form-field instructions-field">
                <span>Special Instructions <span class="required-mark">*</span></span>
                <textarea name="special_instructions" id="special-instructions" rows="2" placeholder="Any special instructions for your lunch?" required></textarea>
            </label>

            <div class="order-total-row"><span>Order Summary</span><strong>Total: <span id="order-total">₱0.00</span></strong></div>
            @error('items')<p class="field-error">{{ $message }}</p>@enderror
            @error('lunch_description')<p class="field-error">{{ $message }}</p>@enderror
            <button class="place-order-button" type="submit">Place Order</button>
            <p class="form-message" id="order-message" role="status" aria-live="polite"></p>
        </section>
    </form>
@endsection

@push('scripts')
<script>
(() => {
    const cart = new Map();
    const typeInput = document.getElementById('order-type-value');
    const cartPayload = document.getElementById('cart-payload');
    const cartItems = document.getElementById('selected-items');
    const countElement = document.getElementById('cart-count');
    const totalElement = document.getElementById('order-total');
    const clearButton = document.getElementById('clear-cart');
    const lunchPanel = document.getElementById('lunch-panel');
    const coffeePanel = document.getElementById('coffee-panel');
    const message = document.getElementById('order-message');
    const money = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
    let category = 'coffee';

    function drawCart() {
        cartItems.replaceChildren();
        let count = 0;
        let total = 0;
        if (!cart.size) {
            const empty = document.createElement('p');
            empty.className = 'empty-cart';
            empty.textContent = 'Your order is empty.';
            cartItems.append(empty);
        }
        cart.forEach((item, id) => {
            count += item.quantity;
            total += item.price * item.quantity;
            const row = document.createElement('div');
            row.className = 'selected-item-row';
            const details = document.createElement('div');
            details.className = 'selected-item-copy';
            const name = document.createElement('strong');
            name.textContent = item.name;
            const price = document.createElement('span');
            price.textContent = money.format(item.price * item.quantity);
            details.append(name, price);
            const controls = document.createElement('div');
            controls.className = 'quantity-control';
            const minus = document.createElement('button');
            minus.type = 'button';
            minus.textContent = '−';
            minus.setAttribute('aria-label', `Remove one ${item.name}`);
            minus.addEventListener('click', () => changeQuantity(id, -1));
            const quantity = document.createElement('span');
            quantity.textContent = item.quantity;
            const plus = document.createElement('button');
            plus.type = 'button';
            plus.textContent = '+';
            plus.setAttribute('aria-label', `Add one ${item.name}`);
            plus.addEventListener('click', () => changeQuantity(id, 1));
            controls.append(minus, quantity, plus);
            row.append(details, controls);
            cartItems.append(row);
        });
        countElement.textContent = `(${count})`;
        totalElement.textContent = money.format(total);
        cartPayload.value = JSON.stringify([...cart.entries()].map(([product_id, item]) => ({ product_id: Number(product_id), quantity: item.quantity })));
        clearButton.hidden = !cart.size;
        document.querySelectorAll('.menu-product-card').forEach((card) => {
            const item = cart.get(card.dataset.productId);
            card.classList.toggle('is-added', Boolean(item));
            card.querySelector('.product-add-mark').textContent = item ? item.quantity : '+';
        });
    }

    function changeQuantity(id, amount) {
        const item = cart.get(String(id));
        if (!item && amount > 0) {
            const card = [...document.querySelectorAll('.menu-product-card')].find((product) => product.dataset.productId === String(id));
            cart.set(String(id), { name: card.dataset.productName, price: Number(card.dataset.productPrice), quantity: 1 });
        } else if (item) {
            item.quantity += amount;
            if (item.quantity < 1) cart.delete(String(id));
        }
        drawCart();
    }

    document.querySelectorAll('.order-type-tab').forEach((tab) => tab.addEventListener('click', () => {
        const isLunch = tab.dataset.orderType === 'lunch';
        typeInput.value = isLunch ? 'lunch' : 'coffee';
        lunchPanel.hidden = !isLunch;
        coffeePanel.hidden = isLunch;
        document.querySelectorAll('.order-type-tab').forEach((item) => {
            const selected = item === tab;
            item.classList.toggle('is-selected', selected);
            item.setAttribute('aria-selected', String(selected));
        });
        document.getElementById('special-instructions').placeholder = isLunch ? 'Any special instructions for your lunch?' : 'Any special instructions for your coffee or pastry?';
        message.textContent = '';
    }));

    document.querySelectorAll('.menu-category-tab').forEach((tab) => tab.addEventListener('click', () => {
        category = tab.dataset.category;
        document.querySelectorAll('.menu-category-tab').forEach((item) => item.classList.toggle('is-selected', item === tab));
        document.querySelectorAll('[data-category-panel]').forEach((panel) => panel.hidden = panel.dataset.categoryPanel !== category);
    }));

    document.querySelectorAll('.menu-product-card').forEach((card) => card.addEventListener('click', () => changeQuantity(card.dataset.productId, 1)));
    clearButton.addEventListener('click', () => { cart.clear(); drawCart(); });
    document.getElementById('customer-order-form').addEventListener('submit', (event) => {
        if (typeInput.value === 'coffee' && cart.size === 0) {
            event.preventDefault();
            message.textContent = 'Choose at least one coffee or pastry.';
        }
    });
    drawCart();
})();
</script>
@endpush