@extends('layouts.admin')
@php $isEdit = isset($item); @endphp
@section('title', $isEdit ? 'Edit Inventory Item' : 'Add Inventory Item')
@section('page-title', $isEdit ? 'Edit Inventory Item' : 'Add Inventory Item')
@section('content')
  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ $isEdit ? route('admin.inventory.update', $item->inventory_id) : route('admin.inventory.store') }}">
        @csrf
        @if($isEdit)
          @method('PUT')
        @endif
        <div class="form-group"><label class="form-label">Item Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required></div>
        <div class="form-group"><label class="form-label">Category</label><input type="text" name="category" class="form-control" placeholder="e.g. Coffee Bean, Syrup, Supplies" value="{{ old('category', $item->category ?? '') }}" required></div>
        <div class="row">
          <div class="form-group" style="flex:1"><label class="form-label">Quantity</label><input type="number" step="0.01" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity ?? '') }}" required></div>
          <div class="form-group" style="flex:1"><label class="form-label">Unit</label><input type="text" name="unit" class="form-control" placeholder="kg, L, pcs" value="{{ old('unit', $item->unit ?? '') }}" required></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="unit_cost">Unit cost (₱)</label>
          <input id="unit_cost" type="number" step="0.01" min="0" name="unit_cost" class="form-control" value="{{ old('unit_cost', $item->unit_cost ?? '') }}" placeholder="Leave blank if unknown">
          <small class="text-muted">Cost per listed unit. Used for valuation: on-hand quantity × unit cost. No price is estimated when blank.</small>
        </div>
        <div class="form-group"><label class="form-label">Low-Stock Threshold</label><input type="number" step="0.01" name="min_threshold" class="form-control" value="{{ old('min_threshold', $item->min_threshold ?? '') }}" required></div>
        <div class="flex gap-12">
          <button class="btn btn-primary">{{ $isEdit ? 'Update Item' : 'Save Item' }}</button>
          <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
