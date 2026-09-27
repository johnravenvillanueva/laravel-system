@extends('layouts.issang')

@section('title', 'Inventory | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Menu and stock control</p><h1>Inventory</h1><p class="page-subtitle">Keep the café menu and stock levels up to date.</p></div>
        <button class="primary-button" type="button" data-open-product-dialog="add">＋ Add Inventory</button>
    </div>

    <form class="inventory-toolbar" method="GET" action="{{ route('admin.inventory') }}">
        <label class="search-field"><span class="visually-hidden">Search inventory</span><input type="search" name="search" value="{{ $search }}" placeholder="Search products"></label>
        <label class="visually-hidden" for="inventory-category">Filter category</label>
        <select class="filter-control" id="inventory-category" name="category">
            <option value="All Types" @selected($category === 'All Types')>All Types</option>
            @foreach ($categories as $option)
                <option value="{{ Str::headline($option) }}" @selected(strtolower($category) === $option)>{{ Str::headline($option) }}</option>
            @endforeach
        </select>
        <button class="quiet-button" type="submit">Apply filters</button>
        @if ($search || $category !== 'All Types')<a class="text-link" href="{{ route('admin.inventory') }}">Clear filters</a>@endif
    </form>

    @if ($products->isEmpty())
        <div class="empty-state"><span class="empty-mark" aria-hidden="true">▧</span><div><strong>No products match those filters</strong><p>Add inventory or change your search to see more items.</p></div></div>
    @else
        <div class="table-scroll inventory-table-wrap">
            <table class="data-table inventory-table">
                <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>In Stock</th><th>Status</th><th class="align-right">Actions</th></tr></thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td><div class="inventory-product"><span class="inventory-thumb">@if ($product->image)<img src="{{ $product->image }}" alt="" loading="lazy">@else<span aria-hidden="true">{{ $product->category === 'coffee' ? '☕' : ($product->category === 'lunch' ? '🍽️' : '🥐') }}</span>@endif</span><span><strong>{{ $product->name }}</strong><small>{{ $product->description }}</small></span></div></td>
                            <td>{{ Str::headline($product->category) }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td><span class="stock-pill {{ $product->stock <= 5 ? 'stock-low' : '' }}">{{ $product->stock }}</span></td>
                            <td><span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">{{ $product->is_active ? 'Active' : 'Archived' }}</span></td>
                            <td class="align-right action-cell">
                                <button class="icon-action" type="button" aria-label="Edit {{ $product->name }}" data-open-product-dialog="edit" data-update-url="{{ route('admin.inventory.update', $product) }}" data-product='@json($product)'>Edit</button>
                                @if ($product->is_active)
                                    <form method="POST" action="{{ route('admin.inventory.destroy', $product) }}" onsubmit="return confirm('Remove {{ addslashes($product->name) }} from the active menu?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="icon-action icon-action-danger" type="submit">Archive</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $products->links() }}</div>
    @endif

    <dialog class="product-dialog" id="product-dialog" aria-labelledby="product-dialog-title">
        <form method="POST" id="product-form" action="{{ route('admin.inventory.store') }}">
            @csrf
            <input type="hidden" name="_method" id="product-method" value="POST">
            <div class="dialog-heading"><div><p class="eyebrow">Menu management</p><h2 id="product-dialog-title">Add Inventory</h2></div><button class="dialog-close" type="button" aria-label="Close" data-close-dialog>×</button></div>
            <div class="dialog-fields">
                <label class="form-field"><span>Product name</span><input name="name" id="product-name" required maxlength="255"></label>
                <label class="form-field"><span>Category</span><select name="category" id="product-category" required><option value="coffee">Coffee</option><option value="pastry">Pastry</option><option value="lunch">Lunch</option></select></label>
                <label class="form-field"><span>Price (PHP)</span><input name="price" id="product-price" type="number" min="0" step="0.01" required></label>
                <label class="form-field"><span>Stock quantity</span><input name="stock" id="product-stock" type="number" min="0" step="1" required></label>
                <label class="form-field dialog-wide"><span>Description</span><textarea name="description" id="product-description" rows="2"></textarea></label>
                <label class="form-field dialog-wide"><span>Image URL</span><input name="image" id="product-image" type="url" placeholder="https://..."></label>
                <input type="hidden" name="is_active" value="0">
                <label class="check-field dialog-wide"><input name="is_active" id="product-active" type="checkbox" value="1" checked><span>Show this product on the menu</span></label>
            </div>
            <div class="dialog-actions"><button class="quiet-button" type="button" data-close-dialog>Cancel</button><button class="primary-button" type="submit" id="product-submit">Save Product</button></div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
(() => {
    const dialog = document.getElementById('product-dialog');
    const form = document.getElementById('product-form');
    const title = document.getElementById('product-dialog-title');
    const method = document.getElementById('product-method');
    const fields = {
        name: document.getElementById('product-name'),
        category: document.getElementById('product-category'),
        price: document.getElementById('product-price'),
        stock: document.getElementById('product-stock'),
        description: document.getElementById('product-description'),
        image: document.getElementById('product-image'),
        active: document.getElementById('product-active'),
    };
    function open(button) {
        const editing = button.dataset.openProductDialog === 'edit';
        title.textContent = editing ? 'Edit Inventory' : 'Add Inventory';
        document.getElementById('product-submit').textContent = editing ? 'Save Changes' : 'Save Product';
        form.action = editing ? button.dataset.updateUrl : '{{ route('admin.inventory.store') }}';
        method.value = editing ? 'PUT' : 'POST';
        Object.values(fields).forEach((field) => field.type === 'checkbox' ? field.checked = true : field.value = '');
        if (editing) {
            const product = JSON.parse(button.dataset.product);
            fields.name.value = product.name;
            fields.category.value = product.category;
            fields.price.value = product.price;
            fields.stock.value = product.stock;
            fields.description.value = product.description || '';
            fields.image.value = product.image || '';
            fields.active.checked = Boolean(product.is_active);
        }
        dialog.showModal();
    }
    document.querySelectorAll('[data-open-product-dialog]').forEach((button) => button.addEventListener('click', () => open(button)));
    document.querySelectorAll('[data-close-dialog]').forEach((button) => button.addEventListener('click', () => dialog.close()));
    dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
})();
</script>
@endpush