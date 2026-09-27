@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')
  <div class="grid grid-4 mb-24">
    <div class="stat-card">
      <div><div class="label">Today's Sales</div><div class="value">₱{{ number_format($todaySales ?? 0,2) }}</div></div>
      <div class="stat-icon">💰</div>
    </div>
    <div class="stat-card">
      <div><div class="label">Pending Orders</div><div class="value">{{ $pendingOrders ?? 0 }}</div></div>
      <div class="stat-icon">📦</div>
    </div>
    <div class="stat-card">
      <div><div class="label">Products</div><div class="value">{{ $productCount ?? 0 }}</div></div>
      <div class="stat-icon">☕</div>
    </div>
    <div class="stat-card">
      <div><div class="label">Low Stock</div><div class="value">{{ $lowStockCount ?? 0 }}</div></div>
      <div class="stat-icon">⚠️</div>
    </div>
  </div>

  <div class="row" style="gap:24px;align-items:flex-start">
    <div class="card" style="flex:2;min-width:320px">
      <div class="card-header">Recent Orders</div>
      <div class="table-wrap" style="border:none;box-shadow:none">
        <table>
          <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
            @forelse($recentOrders ?? [] as $o)
              <tr>
                <td><a href="{{ route('admin.orders.show',$o->id) }}" style="color:var(--brand);font-weight:700">#{{ str_pad($o->id,5,'0',STR_PAD_LEFT) }}</a></td>
                <td>{{ $o->customer->first_name ?? 'Guest' }}</td>
                <td class="bold">₱{{ number_format($o->total_amount,2) }}</td>
                <td><span class="badge badge-{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
                <td class="text-sm text-muted">{{ $o->created_at->format('M d, h:i A') }}</td>
              </tr>
            @empty
              <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">No orders yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card" style="flex:1;min-width:260px">
      <div class="card-header">Low Stock Alerts</div>
      <div class="card-body">
        @forelse($lowStockItems ?? [] as $i)
          <div class="flex between mb-16">
            <div>
              <div class="bold">{{ $i->name }}</div>
              <div class="text-sm text-muted">Threshold: {{ $i->min_threshold }}</div>
            </div>
            <span class="badge badge-low">{{ $i->quantity }} left</span>
          </div>
        @empty
          <p class="text-muted text-sm">All items are well-stocked. ✅</p>
        @endforelse
      </div>
    </div>
  </div>
@endsection