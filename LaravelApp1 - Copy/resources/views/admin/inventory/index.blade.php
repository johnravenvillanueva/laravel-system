@extends('layouts.admin')
@section('title','Inventory')
@section('page-title','Inventory Management')
@section('content')
  <div class="flex between mb-16">
    <form method="GET" class="filter-bar" style="margin:0">
      <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search inventory…" value="{{ request('q') }}"></div>
      <button class="btn btn-primary">Search</button>
    </form>
    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">+ Add Item</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Item</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Threshold</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($items ?? [] as $i)
          <tr>
            <td class="bold">{{ $i->name }}</td>
            <td>{{ $i->category }}</td>
            <td class="bold">{{ $i->quantity }}</td>
            <td>{{ $i->unit }}</td>
            <td>{{ $i->min_threshold }}</td>
            <td>@if($i->quantity <= $i->min_threshold)<span class="badge badge-low">Low</span>@else<span class="badge badge-ok">OK</span>@endif</td>
            <td class="flex gap-8">
              <a href="{{ route('admin.inventory.edit', $i->inventory_id) }}" class="btn btn-outline btn-sm">Edit</a>
<form method="POST" action="{{ route('admin.inventory.destroy', $i->inventory_id) }}" data-confirm="Delete this item?">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px">No inventory items.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection