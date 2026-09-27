@extends('layouts.admin')
@section('title','Edit User')
@section('page-title','Edit User')
@section('content')
  <div class="card" style="max-width:720px">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.update',$user->id) }}">
        @csrf @method('PUT')
        <div class="row">
          <div class="form-group" style="flex:1"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required></div>
          <div class="form-group" style="flex:1"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required></div>
        </div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}" required></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ $user->phone }}"></div>
        <div class="form-group">
          <label class="form-label">Role</label>
          <select name="role" class="form-control">
            @foreach(['admin','staff','customer'] as $r)
              <option value="{{ $r }}" @selected($user->role==$r)>{{ ucfirst($r) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="is_active" class="form-control">
            <option value="1" @selected($user->is_active)>Active</option>
            <option value="0" @selected(!$user->is_active)>Inactive</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">New Password (leave blank to keep)</label><input type="password" name="password" class="form-control"></div>
        <div class="flex gap-12">
          <button class="btn btn-primary">Update User</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection