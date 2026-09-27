let cart = [];

document.addEventListener('DOMContentLoaded', function () {
    initProductSelection();
    initCartActions();
    initPaymentMethods();
    initCheckout();
});

function initProductSelection() {
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);

            addToCart(id, name, price);
            this.classList.add('selected');
            setTimeout(() => this.classList.remove('selected'), 300);
        });
    });
}

function addToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id, name, price, qty: 1 });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
}

function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        removeFromCart(id);
    } else {
        renderCart();
    }
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const countEl = document.getElementById('cartCount');
    const totalEl = document.getElementById('cartTotal');

    if (!container) return;

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="empty-state" style="padding: 32px 16px;">
                <p class="empty-state-text">No items yet. Click a product to add.</p>
            </div>
        `;
    } else {
        container.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${formatCurrency(item.price)}</div>
                </div>
                <div class="cart-qty">
                    <button class="qty-btn" onclick="updateQty('${item.id}', -1)">−</button>
                    <span class="qty-value">${item.qty}</span>
                    <button class="qty-btn" onclick="updateQty('${item.id}', 1)">+</button>
                </div>
                <button class="cart-item-remove" onclick="removeFromCart('${item.id}')">×</button>
            </div>
        `).join('');
    }
   const totalItems = cart.reduce((sum, i) => sum + i.qty, 0);
    const totalAmount = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);

    if (countEl) countEl.textContent = totalItems;
    if (totalEl) totalEl.textContent = formatCurrency(totalAmount);
}

function initCartActions() {
    const clearBtn = document.getElementById('clearCart');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (cart.length && confirm('Clear all items from cart?')) {
                cart = [];
                renderCart();
            }
        });
    }
}

function initPaymentMethods() {
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function initCheckout() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function () {
            if (cart.length === 0) {
                showToast('Cart is empty', 'error');
                return;
            }
            const payment = document.querySelector('.payment-option.active');
            showToast('Sale recorded successfully!', 'success');
            cart = [];
            renderCart();
        });
    }
}