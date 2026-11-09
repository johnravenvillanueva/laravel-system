@extends('layouts.admin')
@section('title','Inventory Report')
@section('page-title','Inventory Report')
@section('content')
  <form method="GET" class="filter-bar">
    <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search item or category" value="{{ request('q') }}"></div>
    <button class="btn btn-primary">Search</button>
    @if(request('q'))<a href="{{ route('admin.reports.inventory') }}" class="btn btn-outline">Clear</a>@endif
    <button type="button" class="btn btn-outline" onclick="window.print()">🖨️ Print</button>
  </form>

  <div class="grid grid-3 mb-24">
    <div class="stat-card"><div><div class="label">Tracked items</div><div class="value">{{ $totalItems ?? 0 }}</div></div><div class="stat-icon">📦</div></div>
    <div class="stat-card stat-card--alert"><div><div class="label">At or below threshold</div><div class="value">{{ $lowCount ?? 0 }}</div></div><div class="stat-icon">!</div></div>
    <div class="stat-card"><div><div class="label">Out of stock</div><div class="value">{{ $outOfStockCount ?? 0 }}</div></div><div class="stat-icon">0</div></div>
    <div class="stat-card stat-card--lead"><div><div class="label">Known stock value</div><div class="value">₱{{ number_format($totalValue ?? 0,2) }}</div></div><div class="stat-icon">₱</div></div>
  </div>
  <div class="card mb-24">
    <div class="card-body">
      <div class="bold">How valuation is calculated</div>
      <div class="text-sm text-muted">Known stock value = sum of each item's on-hand quantity × cost per listed unit. {{ $uncostedCount }} item(s) have no unit cost and are excluded; enter their cost in Inventory Management for a complete valuation.</div>
      @if(request('q'))<div class="text-sm text-muted mt-8">The search filter applies to the items and totals shown below.</div>@endif
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Item</th><th>Category</th><th>On hand</th><th>Unit cost / unit</th><th>Stock value</th><th>Threshold</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($items ?? [] as $i)
          <tr>
            <td class="bold">{{ $i->name }}</td>
            <td>{{ $i->category }}</td>
            <td>{{ $i->quantity }} {{ $i->unit }}</td>
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
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">No items found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection