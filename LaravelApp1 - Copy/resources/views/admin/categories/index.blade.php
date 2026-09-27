@extends('layouts.admin')
@section('title','Categories')
@section('page-title','Category Management')
@section('content')
  <div class="flex between mb-16">
    <h3 class="text-lg bold">All Categories</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Add Category</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Description</th><th>Products</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($categories ?? [] as $c)
          <tr>
            <td class="bold">{{ $c->name }}</td>
            <td class="text-muted">{{ $c->description }}</td>
            <td>{{ $c->products_count ?? 0 }}</td>
            <td class="flex gap-8">
              <a href="{{ route('admin.categories.edit',$c->id) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.categories.destroy',$c->id) }}" data-confirm="Delete this category?">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:30px">No categories yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection