@extends('layouts.admin')
@section('page-title','My Profile')
@section('content')
  <div class="row" style="gap:24px;align-items:flex-start">
    <div class="card" style="width:280px;text-align:center">
      <div class="card-body">
       @if($user->avatar)
  <img src="{{ asset($user->avatar) }}" alt="{{ $user->first_name }}" class="avatar-img avatar-img-lg">
@else
  <div class="avatar avatar-img-lg">{{ strtoupper(substr($user->first_name,0,1)) }}</div>
@endif
        <div class="bold text-lg">{{ $user->first_name }} {{ $user->last_name }}</div>
        <div class="text-sm text-muted">{{ $user->email }}</div>
        <div class="text-sm text-muted mt-8">{{ ucfirst($user->role) }}</div>
      </div>
    </div>

    <div class="card" style="flex:1;min-width:300px">
      <div class="card-header">Edit Information</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
          @csrf @method('PUT')
          <div class="row">
            <div class="form-group" style="flex:1"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}"></div>
            <div class="form-group" style="flex:1"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}"></div>
          </div>
          <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}"></div>
          <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ $user->phone }}"></div>
          <div class="form-group"><label class="form-label">Profile Picture</label><input type="file" name="avatar" class="form-control" accept="image/*"></div>
          <button class="btn btn-primary">Save Changes</button>
        </form>

        <hr style="border:none;border-top:1px solid var(--border);margin:24px 0">

        <h4 class="bold mb-16">Change Password</h4>
        <form method="POST" action="{{ route('admin.profile.password') }}">
          @csrf @method('PUT')
          <div class="form-group"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="form-group"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required></div>
          <div class="form-group"><label class="form-label">Confirm New Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
          <button class="btn btn-outline">Update Password</button>
        </form>
      </div>
    </div>
  </div>
@endsection