@extends('layouts.admin')
@section('title','Inventory Report')
@section('page-title','Inventory Report')
@section('content')
  <form method="GET" class="filter-bar">
    <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search item…" value="{{ request('q') }}"></div>
    <button class="btn btn-primary">Search</button>
    <button type="button" class="btn btn-outline" onclick="window.print()">🖨️ Print</button>
  </form>

  <div class="grid grid-3 mb-24">
    <div class="stat-card"><div><div class="label">Total Items</div><div class="value">{{ $totalItems ?? 0 }}</div></div><div class="stat-icon">📦</div></div>
    <div class="stat-card"><div><div class="label">Low Stock</div><div class="value">{{ $lowCount ?? 0 }}</div></div><div class="stat-icon">⚠️</div></div>
    <div class="stat-card"><div><div class="label">Total Value</div><div class="value">₱{{ number_format($totalValue ?? 0,2) }}</div></div><div class="stat-icon">💵</div></div>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Item</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Threshold</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($items ?? [] as $i)
          <tr>
            <td class="bold">{{ $i->name }}</td>
            <td>{{ $i->category }}</td>
            <td>{{ $i->quantity }}</td>
            <td>{{ $i->unit }}</td>
            <td>{{ $i->min_threshold }}</td>
            <td>@if($i->quantity <= $i->min_threshold)<span class="badge badge-low">Low</span>@else<span class="badge badge-ok">OK</span>@endif</td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">No items.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection