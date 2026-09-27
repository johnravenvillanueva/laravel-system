@extends('layouts.admin')
@section('title','Add Product')
@section('page-title','Add Product')
@section('content')
  <div class="card" style="max-width:720px">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="row">
          <div class="form-group" style="flex:1">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control" required>
              @foreach($categories ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>
          </div>
          <div class="form-group" style="flex:1"><label class="form-label">Price</label><input type="number" step="0.01" name="price" class="form-control" required></div>
        </div>
        <div class="form-group"><label class="form-label">Product Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
        <div class="form-group">
          <label class="form-label">Availability</label>
          <select name="is_available" class="form-control">
            <option value="1">Available</option>
            <option value="0">Unavailable</option>
          </select>
        </div>
        <div class="flex gap-12">
          <button class="btn btn-primary">Save Product</button>
          <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection