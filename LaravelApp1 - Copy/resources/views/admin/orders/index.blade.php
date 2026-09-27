@extends('layouts.admin')
@section('title','Orders')
@section('page-title','Pre-Orders')
@section('content')
  <form method="GET" class="filter-bar">
    <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search order # or customer…" value="{{ request('q') }}"></div>
    <select name="status" class="form-control" style="max-width:180px" onchange="this.form.submit()">
      <option value="">All Status</option>
      @foreach(['pending','confirmed','preparing','ready','completed','cancelled'] as $s)
        <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary">Filter</button>
  </form>

  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Customer</th><th>Pickup</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($orders ?? [] as $o)
          <tr>
            <td class="bold">#{{ str_pad($o->id,5,'0',STR_PAD_LEFT) }}</td>
            <td>{{ $o->customer->first_name ?? 'Guest' }} {{ $o->customer->last_name ?? '' }}</td>
            <td class="text-sm">{{ $o->pickup_date }} {{ $o->pickup_time }}</td>
            <td class="bold">₱{{ number_format($o->total_amount,2) }}</td>
            <td><span class="badge badge-{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
            <td><a href="{{ route('admin.orders.show',$o->id) }}" class="btn btn-outline btn-sm">Manage</a></td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">No orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection