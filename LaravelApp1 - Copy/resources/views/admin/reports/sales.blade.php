@extends('layouts.admin')
@section('title','Sales Report')
@section('page-title','Sales Report')
@section('content')
  <form method="GET" class="filter-bar">
    <select name="range" class="form-control" style="max-width:180px" onchange="this.form.submit()">
      <option value="day" @selected(request('range')=='day')>Today</option>
      <option value="week" @selected(request('range')=='week')>This Week</option>
      <option value="month" @selected(request('range')=='month')>This Month</option>
      <option value="custom" @selected(request('range')=='custom')>Custom Range</option>
    </select>
    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
    <button class="btn btn-primary">Generate</button>
    <button type="button" class="btn btn-outline" onclick="window.print()">🖨️ Print</button>
  </form>

  <div class="grid grid-3 mb-24">
    <div class="stat-card"><div><div class="label">Total Sales</div><div class="value">₱{{ number_format($total ?? 0,2) }}</div></div><div class="stat-icon">💰</div></div>
    <div class="stat-card"><div><div class="label">Transactions</div><div class="value">{{ $count ?? 0 }}</div></div><div class="stat-icon">🧾</div></div>
    <div class="stat-card"><div><div class="label">Average Sale</div><div class="value">₱{{ number_format($avg ?? 0,2) }}</div></div><div class="stat-icon">📊</div></div>
  </div>

  <div class="card mb-24">
    <div class="card-header">Sales Trend</div>
    <div class="card-body">
      <canvas id="salesChart" height="90"></canvas>
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Date</th><th>Customer</th><th>Payment</th><th>Total</th></tr></thead>
      <tbody>
        @forelse($sales ?? [] as $s)
        <tr>
            <td class="bold">#{{ str_pad($s->id,5,'0',STR_PAD_LEFT) }}</td>
            <td class="text-sm">{{ $s->created_at->format('M d, Y h:i A') }}</td>
            <td>{{ $s->customer->first_name ?? 'Walk-in' }}</td>
            <td>{{ ucfirst($s->payment_method) }}</td>
            <td class="bold">₱{{ number_format($s->total_amount,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">No sales in this period.</td></tr>
        @endforelse
    </tbody>
    </table>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
if (ctx) {
new Chart(ctx, {
    type: 'line',
    data: {
    labels: @json($chartLabels ?? []),
    datasets: [{
        label: 'Sales (₱)',
        data: @json($chartData ?? []),
        borderColor: '#6b3e26',
        backgroundColor: 'rgba(107,62,38,.1)',
        fill: true, tension: .3
    }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
}
</script>
@endpush