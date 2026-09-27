@extends('layouts.admin')
@section('title','Products')
@section('page-title','Product Management')
@section('content')
  <div class="flex between mb-16">
    <form method="GET" class="filter-bar" style="margin:0">
      <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search products…" value="{{ request('q') }}"></div>
      <select name="category" class="form-control" style="max-width:180px" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories ?? [] as $c)
          <option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Add Product</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($products ?? [] as $p)
          <tr>
            <td>
              <div class="bold">{{ $p->name }}</div>
              <div class="text-sm text-muted">{{ Str::limit($p->description,60) }}</div>
            </td>
            <td>{{ $p->category->name ?? '—' }}</td>
            <td class="bold">₱{{ number_format($p->price,2) }}</td>
            <td>@if($p->is_available)<span class="badge badge-ok">Available</span>@else<span class="badge badge-cancelled">Unavailable</span>@endif</td>
            <td class="flex gap-8">
              <a href="{{ route('admin.products.edit',$p->id) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.products.destroy',$p->id) }}" data-confirm="Delete this product?">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">No products yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection