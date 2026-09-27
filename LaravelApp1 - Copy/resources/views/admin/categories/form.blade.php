@extends('layouts.admin')
@section('page-title', isset($category) ? 'Edit Category' : 'Add Category')
@section('content')
  <div class="card" style="max-width:600px">
    <div class="card-body">
      <form method="POST" action="{{ isset($category) ? route('admin.categories.update',$category->id) : route('admin.categories.store') }}">
        @csrf
        @isset($category) @method('PUT') @endisset
        <div class="form-group"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name',$category->name ?? '') }}" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control">{{ old('description',$category->description ?? '') }}</textarea></div>
        <div class="flex gap-12">
          <button class="btn btn-primary">{{ isset($category) ? 'Update' : 'Save' }}</button>
          <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection