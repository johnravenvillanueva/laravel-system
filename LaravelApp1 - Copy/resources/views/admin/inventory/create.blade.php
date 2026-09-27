@extends('layouts.admin')
@section('page-title','Add Inventory Item')
@section('content')
  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.inventory.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Item Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Category</label><input type="text" name="category" class="form-control" placeholder="e.g. Coffee Bean, Syrup, Supplies" required></div>
        <div class="row">
          <div class="form-group" style="flex:1"><label class="form-label">Quantity</label><input type="number" step="0.01" name="quantity" class="form-control" required></div>
          <div class="form-group" style="flex:1"><label class="form-label">Unit</label><input type="text" name="unit" class="form-control" placeholder="kg, L, pcs" required></div>
        </div>
        <div class="form-group"><label class="form-label">Low-Stock Threshold</label><input type="number" step="0.01" name="min_threshold" class="form-control" required></div>
        <div class="flex gap-12">
          <button class="btn btn-primary">Save Item</button>
          <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection