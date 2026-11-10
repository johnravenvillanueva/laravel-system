@extends('layouts.admin')
@section('title','Sales Report')
@section('page-title','Sales Report')
@section('content')
  <section class="report-heading">
    <div>
      <div class="report-eyebrow">Business performance</div>
      <h2>Sales report</h2>
      <p>Revenue, transaction mix and product performance for the selected period.</p>
    </div>
    <div class="report-actions">
      <a class="btn btn-outline" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['export' => 'csv'])) }}">Export CSV</a>
      <button type="button" class="btn btn-primary" onclick="window.print()">Print report</button>
    </div>
  </section>

  <form method="GET" class="filter-bar report-filters">
    <label class="report-filter">
      <span>Reporting period</span>
      <select name="range" class="form-control">
        <option value="day" @selected($range === 'day')>Today</option>
        <option value="week" @selected($range === 'week')>This week</option>
        <option value="month" @selected($range === 'month')>This month</option>
        <option value="custom" @selected($range === 'custom')>Custom range</option>
      </select>
    </label>
    <label class="report-filter">
      <span>From</span>
      <input type="date" name="from" class="form-control" value="{{ $range === 'custom' ? $from->toDateString() : request('from') }}">
    </label>
    <label class="report-filter">
      <span>To</span>
      <input type="date" name="to" class="form-control" value="{{ $range === 'custom' ? $to->toDateString() : request('to') }}">
    </label>
    <label class="report-filter">
      <span>Payment</span>
      <select name="payment" class="form-control">
        <option value="">All methods</option>
        <option value="cash" @selected($payment === 'cash')>Cash</option>
        <option value="gcash" @selected($payment === 'gcash')>GCash</option>
      </select>
    </label>
    <label class="report-filter">
      <span>Sale type</span>
      <select name="type" class="form-control">
        <option value="">All sale types</option>
        <option value="walk-in" @selected($type === 'walk-in')>Walk-in</option>
        <option value="pre-order" @selected($type === 'pre-order')>Pre-order</option>
      </select>
    </label>
    <button class="btn btn-primary" type="submit">Generate report</button>
    <a class="btn btn-outline" href="{{ route('admin.reports.sales') }}">Reset</a>
  </form>

  <div class="report-meta">
    <span><strong>Period:</strong> {{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</span>
    <span><strong>Generated:</strong> {{ $generatedAt->format('M d, Y h:i A') }}</span>
    <span><strong>Prepared by:</strong> {{ $generatedBy }}</span>
    <span>All amounts are recorded sales totals; this report does not represent profit.</span>
  </div>

  <div class="grid grid-3 mb-24 report-kpis">
    <article class="stat-card stat-card--lead">
      <div><div class="label">Recorded sales</div><div class="value">₱{{ number_format($total, 2) }}</div></div>
      <div class="stat-icon">₱</div>
      <div class="report-kpi-note">
        @if($changePercent !== null)
          <span class="{{ $changePercent >= 0 ? 'report-positive' : 'report-negative' }}">{{ $changePercent >= 0 ? '+' : '' }}{{ number_format($changePercent, 1) }}%</span>
          vs previous {{ $from->diffInDays($to) + 1 }}-day period (₱{{ number_format($previousTotal, 2) }})
        @else
          No recorded sales in the comparison period
        @endif
      </div>
    </article>
    <article class="stat-card">
      <div><div class="label">Transactions</div><div class="value">{{ number_format($count) }}</div></div>
      <div class="stat-icon">#</div>
      <div class="report-kpi-note">{{ number_format($unitsSold) }} product units sold in this period</div>
    </article>
    <article class="stat-card">
      <div><div class="label">Average transaction</div><div class="value">₱{{ number_format($average, 2) }}</div></div>
      <div class="stat-icon">↗</div>
      <div class="report-kpi-note">Recorded sales divided by transaction count</div>
    </article>
  </div>

  <div class="report-grid mb-24">
    <section class="card">
      <div class="card-header with-meta">
        <span>Sales trend</span>
        <span class="meta">{{ count($chartLabels) }} {{ $from->diffInDays($to) + 1 > 180 ? 'months' : ($from->diffInDays($to) + 1 > 31 ? 'weeks' : 'days') }}</span>
      </div>
      <div class="card-body">
        @if($count > 0)
          <canvas id="salesChart" height="105" aria-label="Sales totals over the selected report period"></canvas>
        @else
          <div class="report-empty">No sales were recorded for the selected filters and period.</div>
        @endif
      </div>
    </section>

    <section class="card">
      <div class="card-header">Sales mix</div>
      <div class="card-body report-breakdowns">
        <div>
          <h3>By payment method</h3>
          @forelse($paymentBreakdown as $row)
            <div class="report-breakdown-row">
              <span>{{ ucfirst($row->payment_method) }} <small>{{ number_format($row->transaction_count) }} transactions</small></span>
              <strong>₱{{ number_format((float) $row->sales_total, 2) }}</strong>
            </div>
          @empty
            <div class="text-sm text-muted">No payment activity for this period.</div>
          @endforelse
        </div>
        <div>
          <h3>By sale type</h3>
          @forelse($typeBreakdown as $row)
            <div class="report-breakdown-row">
              <span>{{ ucfirst($row->transaction_type) }} <small>{{ number_format($row->transaction_count) }} transactions</small></span>
              <strong>₱{{ number_format((float) $row->sales_total, 2) }}</strong>
            </div>
          @empty
            <div class="text-sm text-muted">No sale activity for this period.</div>
          @endforelse
        </div>
      </div>
    </section>
  </div>

  <section class="card mb-24">
    <div class="card-header with-meta">
      <span>Top products</span>
      <span class="meta">Ranked by units sold</span>
    </div>
    <div class="table-wrap report-table-wrap">
      <table>
        <thead><tr><th>Rank</th><th>Product</th><th>Units sold</th><th>Recorded sales</th></tr></thead>
        <tbody>
          @forelse($productPerformance as $index => $product)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td class="bold">{{ $product->product_name }}</td>
              <td>{{ number_format($product->units_sold) }}</td>
              <td class="bold">₱{{ number_format((float) $product->sales_total, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="report-empty">No item-level sales data is available for this period.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="card">
    <div class="card-header with-meta">
      <span>Transaction register</span>
      <span class="meta">{{ number_format($count) }} total · showing {{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }}</span>
    </div>
    <div class="table-wrap report-table-wrap">
      <table>
        <thead><tr><th>Sale ID</th><th>Sale date</th><th>Customer</th><th>Recorded by</th><th>Type</th><th>Payment</th><th class="text-right">Total</th></tr></thead>
        <tbody>
          @forelse($sales as $sale)
            <tr>
              <td class="bold">#{{ str_pad($sale->sale_id, 5, '0', STR_PAD_LEFT) }}</td>
              <td class="text-sm">{{ $sale->sale_date->format('M d, Y h:i A') }}</td>
              <td>{{ trim(($sale->customer?->first_name ?? '').' '.($sale->customer?->last_name ?? '')) ?: 'Walk-in' }}</td>
              <td>{{ trim(($sale->staff?->first_name ?? '').' '.($sale->staff?->last_name ?? '')) ?: '—' }}</td>
              <td>{{ ucfirst($sale->transaction_type) }}</td>
              <td>{{ ucfirst($sale->payment_method) }}</td>
              <td class="bold text-right">₱{{ number_format((float) $sale->total_amount, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="report-empty">No sales in this period.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($sales->hasPages())
      <div class="report-pagination">{{ $sales->links() }}</div>
    @endif
  </section>
@endsection

@push('scripts')
@if($count > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesCanvas = document.getElementById('salesChart');
if (salesCanvas) {
  new Chart(salesCanvas, {
    type: 'line',
    data: {
      labels: @json($chartLabels),
      datasets: [{
        label: 'Recorded sales (₱)',
        data: @json($chartData),
        borderColor: '#6b3e26',
        backgroundColor: 'rgba(107,62,38,.1)',
        fill: true,
        tension: .3
      }]
    },
    options: {
      responsive: true,
      interaction: { intersect: false, mode: 'index' },
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { callback: value => '₱' + Number(value).toLocaleString() } } }
    }
  });
}
</script>
@endif
@endpush
