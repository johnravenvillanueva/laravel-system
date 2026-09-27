document.addEventListener('DOMContentLoaded', function () {
    initOrderTabs();
    initOrderActions();
});

function initOrderTabs() {
    const tabs = document.querySelectorAll('.order-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const status = this.dataset.status;
            const cards = document.querySelectorAll('.order-card');

            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

function initOrderActions() {
    // Status change
    document.querySelectorAll('[data-order-status]').forEach(btn => {
        btn.addEventListener('click', function () {
            const orderId = this.dataset.orderId;
            const newStatus = this.dataset.orderStatus;
            updateOrderStatus(orderId, newStatus);
        });
    });

    // View order details
    document.querySelectorAll('[data-view-order]').forEach(btn => {
        btn.addEventListener('click', function () {
            const orderId = this.dataset.viewOrder;
            openOrderModal(orderId);
        });
    });
}

function updateOrderStatus(orderId, status) {
    const statusLabels = {
        pending: 'Pending',
        confirmed: 'Confirmed',
        preparing: 'Preparing',
        ready: 'Ready for Pickup',
        completed: 'Completed',
        canceled: 'Canceled'
    };

    if (confirm(`Update order ${orderId} to "${statusLabels[status]}"?`)) {
        showToast(`Order ${orderId} updated to ${statusLabels[status]}`, 'success');
        // In real app, this would make an AJAX call
    }
}

function openOrderModal(orderId) {
    const modal = document.getElementById('orderModal');
    if (!modal) return;

    const content = modal.querySelector('.order-modal-content');
    content.innerHTML = `
        <div class="order-detail-header">
            <h3>Order ${orderId}</h3>
            <span class="badge badge-warning">Pending</span>
        </div>
        <div class="order-items-list">
            <div class="order-item-row">
                <span>2x Vanilla Latte</span>
                <span>₱280.00</span>
            </div>
 <div class="order-item-row">
                <span>1x Caramel Macchiato</span>
                <span>₱150.00</span>
            </div>
            <div class="order-item-row">
                <span>1x Strawberry Smoothie</span>
                <span>₱160.00</span>
            </div>
        </div>
        <div class="order-total-row">
            <span>Total</span>
            <span>₱590.00</span>
        </div>
    `;

    modal.classList.add('active');
}

function closeOrderModal() {
    const modal = document.getElementById('orderModal');
    if (modal) modal.classList.remove('active');
}