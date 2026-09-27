document.addEventListener('DOMContentLoaded', function () {
    initInventorySearch();
    initInventoryFilters();
    initDeleteConfirm();
});

function initInventorySearch() {
    const searchInput = document.getElementById('inventorySearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#inventoryTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

function initInventoryFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            const rows = document.querySelectorAll('#inventoryTable tbody tr');

            rows.forEach(row => {
                const status = row.dataset.status;
                if (filter === 'all' || status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
}

function initDeleteConfirm() {
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const itemName = this.dataset.confirmDelete;
            if (confirm(`Are you sure you want to delete "${itemName}"?`)) {
                showToast('Item deleted successfully', 'success');
                this.closest('tr').remove();
            }
        });
    });
}

// Inventory form handling
const inventoryForm = document.getElementById('inventoryForm');
if (inventoryForm) {
    inventoryForm.addEventListener('submit', function (e) {
        e.preventDefault();
        showToast('Inventory item saved successfully', 'success');
        setTimeout(() => {
            window.location.href = '/admin/inventory';
        }, 1200);
    });
}