@extends('layouts.issang')

@section('title', 'Sales Reports | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Sales performance</p><h1>Sales Reports</h1><p class="page-subtitle">Review completed transactions and top-selling items.</p></div>
        <div class="report-actions"><button type="button" class="quiet-button" onclick="window.print()">Print Report</button><a class="secondary-button" href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}">Download CSV</a></div>
    </div>

    <section class="report-filter-bar">
        <div class="quick-date-filters" aria-label="Quick report ranges">
            <button type="button" class="filter-tab" data-range="today">Today</button>
            <button type="button" class="filter-tab" data-range="7">Last 7 Days</button>
            <button type="button" class="filter-tab" data-range="30">Last 30 Days</button>
            <button type="button" class="filter-tab" data-range="90">Last 90 Days</button>
        </div>
        <form method="GET" action="{{ route('admin.reports') }}" class="date-range-form" id="report-date-form">
            <label class="form-field"><span>Start Date</span><input type="date" name="start_date" id="report-start" value="{{ $startDate }}" required></label>
            <label class="form-field"><span>End Date</span><input type="date" name="end_date" id="report-end" value="{{ $endDate }}" required></label>
            <button class="primary-button" type="submit">Generate Report</button>
        </form>
    </section>

    <section class="stats-grid report-stats">
        <article class="stat-card"><span class="stat-label">Revenue</span><strong>₱{{ number_format($revenue, 2) }}</strong><small>{{ $orders->count() }} completed transactions</small></article>
        <article class="stat-card"><span class="stat-label">Items Sold</span><strong>{{ number_format($quantity) }}</strong><small>Across selected dates</small></article>
        <article class="stat-card"><span class="stat-label">Cash</span><strong>₱{{ number_format($cashTotal, 2) }}</strong><small>Cash payments</small></article>
        <article class="stat-card"><span class="stat-label">GCash</span><strong>₱{{ number_format($gcashTotal, 2) }}</strong><small>Digital payments</small></article>
    </section>

    <div class="report-columns">
        <section class="data-section report-chart-section">
            <div class="section-heading-row"><div><p class="eyebrow">By transaction date</p><h2>Daily Breakdown</h2></div><span class="report-period">{{ \Carbon\Carbon::parse($startDate)->format('M j') }} – {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</span></div>
            @php $daily = $orders->groupBy(fn ($order) => $order->created_at->format('M j')); $maxDaily = max(1, (float) $daily->max(fn ($items) => $items->sum('total'))); @endphp
            @if ($daily->isEmpty())
                <div class="empty-inline">No completed sales in this date range.</div>
            @else
                <div class="daily-breakdown">
                    @foreach ($daily as $day => $dayOrders)
                        @php $amount = (float) $dayOrders->sum('total'); @endphp
                        <div class="daily-row"><span>{{ $day }}</span><span class="daily-track"><i style="width: {{ max(4, $amount / $maxDaily * 100) }}%"></i></span><strong>₱{{ number_format($amount, 2) }}</strong></div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="data-section report-top-products">
            <div class="section-heading-row"><div><p class="eyebrow">By quantity sold</p><h2>Top Selling Items</h2></div></div>
            @if ($topProducts->isEmpty())
                <div class="empty-inline">Product rankings will appear after sales are recorded.</div>
            @else
                <div class="ranking-list">
                    @foreach ($topProducts as $index => $product)
                        <div class="ranking-row"><span class="ranking-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $product['name'] }}</strong><span>{{ $product['quantity'] }} sold</span><b>₱{{ number_format($product['revenue'], 2) }}</b></div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const start = document.getElementById('report-start');
    const end = document.getElementById('report-end');
    const today = new Date();
    const format = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    document.querySelectorAll('[data-range]').forEach((button) => button.addEventListener('click', () => {
        const from = new Date(today);
        if (button.dataset.range === 'today') from.setHours(0, 0, 0, 0);
        else from.setDate(from.getDate() - Number(button.dataset.range) + 1);
        start.value = format(from);
        end.value = format(today);
        document.getElementById('report-date-form').requestSubmit();
    }));
})();
</script>
@endpush