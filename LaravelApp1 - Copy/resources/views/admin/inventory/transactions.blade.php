@extends('layouts.admin')
@section('title','Inventory Transactions')
@section('page-title','Inventory Transactions')
@section('content')
  <div class="flex between mb-16">
    <div class="text-sm text-muted">Audited stock movements, including manual counts and sales usage.</div>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline">Back to inventory</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Item</th><th>Type</th><th>Qty</th><th>Reference</th><th>By</th><th>Notes</th></tr></thead>
      <tbody>
        @forelse($transactions ?? [] as $t)
          <tr>
            <td class="text-sm text-muted">{{ $t->transaction_date?->format('M d, Y h:i A') ?? '—' }}</td>
            <td class="bold">{{ $t->inventory->name ?? 'Deleted item' }}</td>
            <td><span class="badge badge-{{ $t->transaction_type === 'deduct' ? 'cancelled' : 'ok' }}">{{ ucfirst($t->transaction_type) }}</span></td>
            <td>{{ number_format((float) $t->quantity, 2) }} {{ $t->inventory->unit ?? '' }}</td>
            <td>{{ $t->reference_type ? \Illuminate\Support\Str::headline($t->reference_type) : 'Manual' }}{{ $t->reference_id ? ' #'.$t->reference_id : '' }}</td>
            <td>{{ $t->performedBy?->fullName() ?? 'System' }}</td>
            <td class="text-sm text-muted">{{ Str::limit($t->notes,40) }}</td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">No transactions recorded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-16">{{ $transactions->links() }}</div>
@endsection