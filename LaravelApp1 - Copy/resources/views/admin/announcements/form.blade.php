@extends('layouts.admin')
@section('page-title', isset($announcement) ? 'Edit Announcement' : 'New Announcement')
@section('content')
  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ isset($announcement) ? route('admin.announcements.update',$announcement->id) : route('admin.announcements.store') }}">
        @csrf
        @isset($announcement) @method('PUT') @endisset
        <div class="form-group"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="{{ old('title',$announcement->title ?? '') }}" required></div>
        <div class="form-group"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="6" required>{{ old('content',$announcement->content ?? '') }}</textarea></div>
        <div class="row">
          <div class="form-group" style="flex:1">
            <label class="form-label">Target Audience</label>
            <select name="target_audience" class="form-control">
              <option value="all" @selected(old('target_audience',$announcement->target_audience ?? '')=='all')>All</option>
              <option value="customers" @selected(old('target_audience',$announcement->target_audience ?? '')=='customers')>Customers</option>
              <option value="staff" @selected(old('target_audience',$announcement->target_audience ?? '')=='staff')>Staff</option>
            </select>
          </div>
          <div class="form-group" style="flex:1">
            <label class="form-label">Expires At (optional)</label>
            <input type="datetime-local" name="expires_at" class="form-control"
                   value="{{ old('expires_at', isset($announcement) && $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="is_active" class="form-control">
            <option value="1" @selected(old('is_active',$announcement->is_active ?? 1))>Active</option>
            <option value="0" @selected(!old('is_active',$announcement->is_active ?? 1))>Inactive</option>
          </select>
        </div>
        <div class="flex gap-12">
          <button class="btn btn-primary">{{ isset($announcement) ? 'Update' : 'Publish' }}</button>
          <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection