@extends('layouts.admin')
@section('title','Manage Order')
@section('page-title','Manage Order')
@section('content')
  <a href="{{ route('admin.orders.index') }}" class="text-sm" style="color:var(--brand)">← Back to orders</a>
  <div class="flex between center mt-16 mb-16">
    <h2 class="text-xl bold">Order #{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}</h2>
    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
  </div>

  <div class="row" style="gap:24px;align-items:flex-start">
    <div class="card" style="flex:2;min-width:320px">
      <div class="card-header">Order Items</div>
      <div class="table-wrap" style="border:none;box-shadow:none">
        <table>
          <thead><tr><th>Product</th><th>Qty</th><th>Unit</th><th>Subtotal</th><th>Note</th></tr></thead>
          <tbody>
            @foreach($order->items as $i)
              <tr>
                <td>{{ $i->product->name }}</td>
                <td>{{ $i->quantity }}</td>
                <td>₱{{ number_format($i->unit_price,2) }}</td>
                <td class="bold">₱{{ number_format($i->subtotal,2) }}</td>
                <td class="text-sm text-muted">{{ $i->special_instructions }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-body flex between">
        <span class="bold">Total</span>
        <span class="bold text-lg" style="color:var(--brand)">₱{{ number_format($order->total_amount,2) }}</span>
      </div>
    </div>

    <div class="card" style="flex:1;min-width:300px">
      <div class="card-header">Customer & Pickup</div>
      <div class="card-body">
        <div class="bold">{{ $order->customer->first_name ?? 'Guest' }} {{ $order->customer->last_name ?? '' }}</div>
        <div class="text-sm text-muted">{{ $order->customer->email ?? '' }}</div>
        <div class="text-sm text-muted">{{ $order->customer->phone ?? '' }}</div>
        <hr style="border:none;border-top:1px solid var(--border);margin:16px 0">
        <div class="text-sm"><strong>Pickup Date:</strong> {{ $order->pickup_date }}</div>
        <div class="text-sm mt-8"><strong>Pickup Time:</strong> {{ $order->pickup_time }}</div>
        <div class="text-sm mt-8"><strong>Payment:</strong> {{ ucfirst($order->payment_method) }}</div>
        <div class="text-sm mt-8"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</div>

        <form method="POST" action="{{ route('admin.orders.status',$order->id) }}" class="mt-24">
          @csrf @method('PUT')
          <div class="form-group">
            <label class="form-label">Update Status</label>
            <select name="status" class="form-control">
              @foreach(['pending','confirmed','preparing','ready','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected($order->status==$s)>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <button class="btn btn-primary btn-block">Update Order</button>
        </form>
      </div>
    </div>
  </div>
@endsection