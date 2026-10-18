@extends('layouts.admin')
@section('title','Sales')
@section('page-title','Sales Records')
@section('content')
  <div class="flex between mb-16">
    <form method="GET" class="filter-bar" style="margin:0">
      <input type="date" name="from" class="form-control" value="{{ request('from') }}">
      <input type="date" name="to" class="form-control" value="{{ request('to') }}">
      
      <button class="btn btn-primary">Filter</button>
    </form>
    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary">+ New Walk-in Sale</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Date</th><th>Customer</th><th>Type</th><th>Payment</th><th>Total</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($sales ?? [] as $s)
          <tr>
            <td class="bold">#{{ str_pad($s->id,5,'0',STR_PAD_LEFT) }}</td>
            <td class="text-sm">{{ $s->created_at->format('M d, Y h:i A') }}</td>
            <td>{{ $s->customer->first_name ?? 'Walk-in' }}</td>
            <td>{{ ucfirst($s->transaction_type) }}</td>
            <td>{{ ucfirst($s->payment_method) }}</td>
            <td class="bold">₱{{ number_format($s->total_amount,2) }}</td>
            <td><a href="{{ route('admin.sales.show',$s->id) }}" class="btn btn-outline btn-sm">View</a></td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">No sales records.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection