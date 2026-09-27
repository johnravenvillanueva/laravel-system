@extends('layouts.admin')
@section('title','Add User')
@section('page-title','Add User')
@section('content')
  <div class="card" style="max-width:720px">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="row">
          <div class="form-group" style="flex:1"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" required></div>
          <div class="form-group" style="flex:1"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" required></div>
        </div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
        <div class="form-group">
          <label class="form-label">Role</label>
          <select name="role" class="form-control" required>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
            <option value="customer">Customer</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
        <div class="flex gap-12">
          <button class="btn btn-primary">Save User</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection