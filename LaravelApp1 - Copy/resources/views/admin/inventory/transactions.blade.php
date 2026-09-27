@extends('layouts.admin')
@section('title','Inventory Transactions')
@section('page-title','Inventory Transactions')
@section('content')
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Item</th><th>Type</th><th>Qty</th><th>Reference</th><th>By</th><th>Notes</th></tr></thead>
      <tbody>
        @forelse($transactions ?? [] as $t)
          <tr>
            <td class="text-sm text-muted">{{ $t->transaction_date }}</td>
            <td class="bold">{{ $t->inventory->name ?? '—' }}</td>
            <td><span class="badge badge-{{ $t->transaction_type === 'deduct' ? 'cancelled' : 'ok' }}">{{ ucfirst($t->transaction_type) }}</span></td>
            <td>{{ $t->quantity }}</td>
            <td>{{ $t->reference_type }} #{{ $t->reference_id }}</td>
            <td>{{ $t->performed_by }}</td>
            <td class="text-sm text-muted">{{ Str::limit($t->notes,40) }}</td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">No transactions recorded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection