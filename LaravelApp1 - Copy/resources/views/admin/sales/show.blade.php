@extends('layouts.admin')
@section('title','Sale Details')
@section('page-title','Sale Details')
@section('content')
  <a href="{{ route('admin.sales.index') }}" class="text-sm" style="color:var(--brand)">← Back to sales</a>
  <h2 class="text-xl bold mt-16 mb-16">Sale #{{ str_pad($sale->id,5,'0',STR_PAD_LEFT) }}</h2>

  <div class="row" style="gap:24px;align-items:flex-start">
    <div class="card" style="flex:2;min-width:320px">
      <div class="card-header">Items</div>
      <div class="table-wrap" style="border:none;box-shadow:none">
        <table>
          <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
          <tbody>
            @foreach($sale->items as $i)
              <tr>
                <td>{{ $i->product->name }}</td>
                <td>{{ $i->quantity }}</td>
                <td>₱{{ number_format($i->unit_price,2) }}</td>
                <td class="bold">₱{{ number_format($i->subtotal,2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="card" style="flex:1;min-width:280px">
      <div class="card-header">Summary</div>
      <div class="card-body">
        <div class="flex between mb-16"><span>Total</span><span class="bold text-lg" style="color:var(--brand)">₱{{ number_format($sale->total_amount,2) }}</span></div>
        <div class="text-sm"><strong>Payment:</strong> {{ ucfirst($sale->payment_method) }}</div>
        <div class="text-sm mt-8"><strong>Type:</strong> {{ ucfirst($sale->transaction_type) }}</div>
        <div class="text-sm mt-8"><strong>Date:</strong> {{ $sale->created_at->format('M d, Y h:i A') }}</div>
      </div>
    </div>
  </div>
@endsection