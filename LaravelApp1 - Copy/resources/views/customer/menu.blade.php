@extends('layouts.app')
@section('title','Menu · Issang Macchiatos')
@section('content')
  <h2 class="text-2xl bold mb-16">Our Menu</h2>

  <form method="GET" class="filter-bar">
    <div class="search-box">
      <input type="text" name="q" class="form-control" placeholder="Search products…" value="{{ request('q') }}">
    </div>
    <select name="category" class="form-control" style="max-width:220px" onchange="this.form.submit()">
      <option value="">All Categories</option>
      @foreach($categories ?? [] as $c)
        <option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary">Search</button>
  </form>

  <div class="grid grid-4">
    @forelse($products ?? [] as $p)
      @include('components.product-card', ['product' => $p])
    @empty
      <p class="text-muted">No products found.</p>
    @endforelse
  </div>
@endsection