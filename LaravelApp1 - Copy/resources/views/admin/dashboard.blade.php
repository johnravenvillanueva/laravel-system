@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('content')

  @php
    // Last 7 days, oldest first, filling days with no sales with 0
    $series = collect(range(6, 0))->map(function ($daysAgo) use ($chartData) {
        $date = now()->subDays($daysAgo);
        return [
            'label' => $date->format('D'),
            'full'  => $date->format('M j'),
            'value' => (float) (($chartData ?? collect())->get($date->toDateString(), 0)),
            'today' => $daysAgo === 0,
        ];
    });
    $peak = max($series->max('value'), 1);
    $weekTotal = $series->sum('value');
  @endphp

  <div class="grid grid-4 mb-24">
    <div class="stat-card stat-card--lead">
      <div><div class="label">Today's sales</div><div class="value">₱{{ number_format($todaySales ?? 0, 2) }}</div></div>
      <div class="stat-icon">@include('partials.icon', ['ico' => 'banknote'])</div>
    </div>
    <div class="stat-card">
      <div><div class="label">Pending orders</div><div class="value">{{ $pendingOrders ?? 0 }}</div></div>
      <div class="stat-icon">@include('partials.icon', ['ico' => 'package'])</div>
    </div>
    <div class="stat-card">
      <div><div class="label">Products</div><div class="value">{{ $productCount ?? 0 }}</div></div>
      <div class="stat-icon">@include('partials.icon', ['ico' => 'coffee'])</div>
    </div>
    <div class="stat-card {{ ($lowStockCount ?? 0) > 0 ? 'stat-card--alert' : '' }}">
      <div><div class="label">Low stock items</div><div class="value">{{ $lowStockCount ?? 0 }}</div></div>
      <div class="stat-icon">@include('partials.icon', ['ico' => 'alert'])</div>
    </div>
  </div>

  <div class="dash-grid">
    <div class="card">
      <div class="card-header with-meta">
        <span>Sales, last 7 days</span>
        <span class="meta">₱{{ number_format($weekTotal, 2) }} total</span>
      </div>
      <div class="card-body">
        <div class="bars" role="img" aria-label="Daily sales for the last 7 days">
          @foreach($series as $d)
            <div class="bar {{ $d['value'] <= 0 ? 'is-empty' : '' }} {{ $d['today'] ? 'is-today' : '' }}" title="{{ $d['full'] }}: ₱{{ number_format($d['value'], 2) }}">
              <span class="bar-value">{{ $d['value'] > 0 ? '₱' . number_format($d['value'], 0) : '' }}</span>
              <div class="bar-fill" style="height: {{ max(2, round($d['value'] / $peak * 70)) }}%"></div>
              <span class="bar-label">{{ $d['label'] }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Low stock alerts</div>
      <div class="card-body">
        @forelse($lowStockItems ?? [] as $i)
          <div class="stock-row">
            <div>
              <div class="bold">{{ $i->name }}</div>
              <div class="text-sm text-muted">Reorder at {{ $i->min_threshold }}</div>
            </div>
            <span class="badge badge-low">{{ $i->quantity }} left</span>
          </div>
        @empty
          <p class="empty-note">Everything is well stocked.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header with-meta">
      <span>Recent orders</span>
      <a href="{{ route('admin.orders.index') }}" class="link-arrow">View all @include('partials.icon', ['ico' => 'arrow-right'])</a>
    </div>
    <div class="table-wrap" style="border:none;border-radius:0 0 var(--radius) var(--radius)">
      <table>
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          @forelse($recentOrders ?? [] as $o)
            <tr>
              <td><a href="{{ route('admin.orders.show', $o->order_id) }}" style="color:var(--brand);font-weight:700">#{{ str_pad($o->order_id, 5, '0', STR_PAD_LEFT) }}</a></td>
              <td>{{ $o->customer->first_name ?? 'Guest' }} {{ $o->customer->last_name ?? '' }}</td>
              <td class="bold">₱{{ number_format($o->total_amount, 2) }}</td>
              <td><span class="badge badge-{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
              <td class="text-sm text-muted">{{ $o->created_at->format('M d, h:i A') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="empty-note">No orders yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
