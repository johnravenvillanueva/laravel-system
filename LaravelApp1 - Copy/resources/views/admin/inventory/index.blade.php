@extends('layouts.admin')
@section('title','Inventory')
@section('page-title','Inventory Management')
@section('content')
  <div class="flex between mb-16" style="gap:12px;flex-wrap:wrap">
    <form method="GET" class="filter-bar" style="margin:0">
      <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search item or category" value="{{ request('q') }}"></div>
      <button class="btn btn-primary">Search</button>
      @if(request('q'))<a href="{{ route('admin.inventory.index') }}" class="btn btn-outline">Clear</a>@endif
    </form>
    <div class="flex gap-8">
      <a href="{{ route('admin.inventory.transactions') }}" class="btn btn-outline">Stock history</a>
      <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">+ Add Item</a>
    </div>
  </div>

  <div class="grid grid-3 mb-24">
    <div class="stat-card"><div><div class="label">Tracked items</div><div class="value">{{ $summary->total_items ?? 0 }}</div></div><div class="stat-icon">📦</div></div>
    <div class="stat-card stat-card--alert"><div><div class="label">At or below threshold</div><div class="value">{{ $summary->low_count ?? 0 }}</div></div><div class="stat-icon">!</div></div>
    <div class="stat-card"><div><div class="label">Out of stock</div><div class="value">{{ $summary->out_count ?? 0 }}</div></div><div class="stat-icon">0</div></div>
    <div class="stat-card"><div><div class="label">Known stock value</div><div class="value">₱{{ number_format((float) ($summary->total_value ?? 0), 2) }}</div></div><div class="stat-icon">₱</div></div>
  </div>
  @if(($summary->uncosted_count ?? 0) > 0)
    <div class="flash flash-info mb-16" role="status">
      {{ $summary->uncosted_count }} item(s) have no unit cost. They are excluded from stock valuation until a cost is entered.
    </div>
  @endif

  <form method="POST" action="{{ route('admin.inventory.bulk-update') }}">
    @csrf
    <div class="card mb-16">
      <div class="card-body flex between" style="gap:16px;flex-wrap:wrap">
        <div>
          <div class="bold">Bulk stock count</div>
          <div class="text-sm text-muted">Select items and enter each item's new on-hand quantity. Changes are recorded in stock history.</div>
        </div>
        <div class="flex gap-8" style="flex-wrap:wrap">
          <input type="text" name="notes" class="form-control" style="width:min(360px,100%)" maxlength="1000" placeholder="Reason for stock count (required)" value="{{ old('notes') }}" required>
          <button type="submit" class="btn btn-primary">Update selected stock</button>
        </div>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead><tr>
          <th><input type="checkbox" id="selectAllInventory" aria-label="Select all items on this page"></th>
          <th>Item</th><th>Category</th><th>On hand</th><th>Unit cost / unit</th><th>Stock value</th><th>Threshold</th><th>Status</th><th>Actions</th>
        </tr></thead>
        <tbody>
          @forelse($items as $i)
            <tr>
              <td><input type="checkbox" name="selected[]" value="{{ $i->inventory_id }}" class="inventory-select" aria-label="Select {{ $i->name }}"></td>
              <td class="bold">{{ $i->name }}</td>
              <td>{{ $i->category }}</td>
              <td><input type="number" step="0.01" min="0" max="99999999.99" name="quantities[{{ $i->inventory_id }}]" class="form-control" style="width:130px" value="{{ old('quantities.'.$i->inventory_id, $i->quantity) }}" aria-label="New on-hand quantity for {{ $i->name }}"></td>
              <td>{{ $i->unit_cost === null ? 'Not set' : '₱'.number_format((float) $i->unit_cost, 2).' / '.$i->unit }}</td>
              <td>{{ $i->unit_cost === null ? '—' : '₱'.number_format((float) $i->quantity * (float) $i->unit_cost, 2) }}</td>
              <td>{{ $i->min_threshold }} {{ $i->unit }}</td>
              <td>
                @if((float) $i->quantity <= 0)
                  <span class="badge badge-out">Out of stock</span>
                @elseif($i->isLowStock())
                  <span class="badge badge-low">Low</span>
                @else
                  <span class="badge badge-ok">In stock</span>
                @endif
              </td>
              <td><a href="{{ route('admin.inventory.edit', $i->inventory_id) }}" class="btn btn-outline btn-sm">Edit</a></td>
            </tr>
          @empty
            <tr><td colspan="9" style="text-align:center;color:var(--muted);padding:30px">No inventory items found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-16">{{ $items->links() }}</div>
  </form>
@endsection

@push('scripts')
<script>
document.getElementById('selectAllInventory')?.addEventListener('change', function () {
  document.querySelectorAll('.inventory-select').forEach((checkbox) => {
    checkbox.checked = this.checked;
  });
});
</script>
@endpush