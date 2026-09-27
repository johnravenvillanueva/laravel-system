let selectedOrderType = null;
let selectedMenuItems = [];

document.addEventListener('DOMContentLoaded', function () {
    initMobileNav();
    initOrderTypeSelection();
    initMenuSelection();
    initOrderForm();
});

function initMobileNav() {
    const toggle = document.getElementById('mobileNavToggle');
    const nav = document.querySelector('.customer-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            nav.classList.toggle('open');
        });
    }
}

function initOrderTypeSelection() {
    document.querySelectorAll('.order-type-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.order-type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedOrderType = this.dataset.type;

            // Show/hide relevant sections
            const lunchSection = document.getElementById('lunchDetails');
            const coffeeSection = document.getElementById('coffeeDetails');

            if (selectedOrderType === 'lunch') {
                if (lunchSection) lunchSection.style.display = 'block';
                if (coffeeSection) coffeeSection.style.display = 'none';
            } else if (selectedOrderType === 'coffee') {
                if (lunchSection) lunchSection.style.display = 'none';
                if (coffeeSection) coffeeSection.style.display = 'block';
            } else {
                if (lunchSection) lunchSection.style.display = 'block';
                if (coffeeSection) coffeeSection.style.display = 'block';
            }
        });
    });
}

function initMenuSelection() {
    document.querySelectorAll('.menu-card').forEach(card => {
        card.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);

            const index = selectedMenuItems.findIndex(i => i.id === id);
            if (index > -1) {
                selectedMenuItems.splice(index, 1);
                this.classList.remove('selected');
            } else {
                selectedMenuItems.push({ id, name, price });
                this.classList.add('selected');
            }

            updateEstimatedAmount();
        });
    });
}

function updateEstimatedAmount() {
    const amountInput = document.getElementById('estimatedAmount');
    if (amountInput) {
        const total = selectedMenuItems.reduce((sum, i) => sum + i.price, 0);
        amountInput.value = total.toFixed(2);
    }
}

function initOrderForm() {
    const form = document.getElementById('preOrderForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!selectedOrderType) {
                showToast('Please select an order type', 'error');
                return;
            }

            const pickupDate = document.getElementById('pickupDate').value;
            const pickupTime = document.getElementById('pickupTime').value;

            if (!pickupDate || !pickupTime) {
                showToast('Please select pickup date and time', 'error');
                return;
            }

            showToast('Pre-order placed successfully!', 'success');
            setTimeout(() => {
                window.location.href = '/customer/orders';
            }, 1500);
        });
    }
}