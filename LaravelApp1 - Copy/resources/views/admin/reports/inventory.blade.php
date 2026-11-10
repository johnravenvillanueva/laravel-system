@extends('layouts.admin')
@section('title','Inventory Report')
@section('page-title','Inventory Report')
@section('content')
  <section class="report-heading">
    <div>
      <div class="report-eyebrow">Stock health &amp; valuation</div>
      <h2>Inventory report</h2>
      <p>Current on-hand quantities, replenishment risk and recorded stock value.</p>
    </div>
    <div class="report-actions">
      <a class="btn btn-outline" href="{{ route('admin.reports.inventory', array_merge(request()->query(), ['export' => 'csv'])) }}">Export CSV</a>
      <button type="button" class="btn btn-primary" onclick="window.print()">Print report</button>
    </div>
  </section>

  <form method="GET" class="filter-bar report-filters">
    <label class="report-filter report-filter--search">
      <span>Item or category</span>
      <input type="search" name="q" class="form-control" placeholder="Search inventory" value="{{ $search }}">
    </label>
    <label class="report-filter">
      <span>Stock status</span>
      <select name="status" class="form-control">
        <option value="">All statuses</option>
        <option value="low" @selected($status === 'low')>Low stock</option>
        <option value="out" @selected($status === 'out')>Out of stock</option>
        <option value="ok" @selected($status === 'ok')>In stock</option>
      </select>
    </label>
    <button class="btn btn-primary" type="submit">Apply filters</button>
    <a class="btn btn-outline" href="{{ route('admin.reports.inventory') }}">Reset</a>
  </form>

  <div class="report-meta">
    <span><strong>As of:</strong> {{ $generatedAt->format('M d, Y h:i A') }}</span>
    <span><strong>Prepared by:</strong> {{ $generatedBy }}</span>
    @if($search || $status)
      <span>Totals and category summaries reflect the current filters.</span>
    @else
      <span>Current stock snapshot across all tracked inventory.</span>
    @endif
  </div>

  <div class="grid grid-3 mb-24 report-kpis">
    <article class="stat-card">
      <div><div class="label">Tracked items</div><div class="value">{{ number_format($totalItems) }}</div></div>
      <div class="stat-icon">#</div>
      <div class="report-kpi-note">Distinct stock records in this view</div>
    </article>
    <article class="stat-card stat-card--alert">
      <div><div class="label">Replenishment alerts</div><div class="value">{{ number_format($lowCount) }}</div></div>
      <div class="stat-icon">!</div>
      <div class="report-kpi-note">At or below minimum; includes out-of-stock items</div>
    </article>
    <article class="stat-card">
      <div><div class="label">Out of stock</div><div class="value">{{ number_format($outOfStockCount) }}</div></div>
      <div class="stat-icon">0</div>
      <div class="report-kpi-note">Items with zero or negative on-hand quantity</div>
    </article>
    <article class="stat-card stat-card--lead">
      <div><div class="label">Known stock value</div><div class="value">₱{{ number_format((float) $totalValue, 2) }}</div></div>
      <div class="stat-icon">₱</div>
      <div class="report-kpi-note">{{ number_format($uncostedCount) }} item(s) have no unit cost</div>
    </article>
  </div>

  <div class="card mb-24">
    <div class="card-body report-methodology">
      <strong>Valuation and alert methodology</strong>
      <p>Known stock value is calculated as on-hand quantity × recorded unit cost for each costed item. Items without a recorded unit cost are excluded from the value total. Replenishment alerts are triggered when on-hand quantity is at or below the item's minimum threshold. Values are an operational estimate, not an accounting inventory valuation.</p>
    </div>
  </div>

  <section class="card mb-24">
    <div class="card-header with-meta">
      <span>Stock by category</span>
      <span class="meta">{{ $categoryBreakdown->count() }} categories</span>
    </div>
    <div class="table-wrap report-table-wrap">
      <table>
        <thead><tr><th>Category</th><th>Items</th><th>Replenishment alerts</th><th>Out of stock</th><th class="text-right">Known stock value</th></tr></thead>
        <tbody>
          @forelse($categoryBreakdown as $category)
            <tr>
              <td class="bold">{{ $category->category }}</td>
              <td>{{ number_format($category->item_count) }}</td>
              <td>{{ number_format($category->low_count) }}</td>
              <td>{{ number_format($category->out_count) }}</td>
              <td class="bold text-right">₱{{ number_format((float) $category->known_value, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="report-empty">No inventory categories match these filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="card">
    <div class="card-header with-meta">
      <span>Inventory register</span>
      <span class="meta">{{ number_format($totalItems) }} items</span>
    </div>
    <div class="table-wrap report-table-wrap">
      <table>
        <thead><tr><th>Item</th><th>Category</th><th>On hand</th><th>Unit cost</th><th>Stock value</th><th>Minimum</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($items as $item)
            <tr>
              <td class="bold">{{ $item->name }}</td>
              <td>{{ $item->category }}</td>
              <td>{{ number_format((float) $item->quantity, 2) }} {{ $item->unit }}</td>
              <td>{{ $item->unit_cost === null ? 'Not set' : '₱'.number_format((float) $item->unit_cost, 2).' / '.$item->unit }}</td>
              <td>{{ $item->unit_cost === null ? '—' : '₱'.number_format((float) $item->quantity * (float) $item->unit_cost, 2) }}</td>
              <td>{{ number_format((float) $item->min_threshold, 2) }} {{ $item->unit }}</td>
              <td>
                @if((float) $item->quantity <= 0)
                  <span class="badge badge-out">Out of stock</span>
                @elseif($item->isLowStock())
                  <span class="badge badge-low">Reorder</span>
                @else
                  <span class="badge badge-ok">In stock</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="report-empty">No inventory items match these filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
