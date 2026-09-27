@extends('layouts.auth')
@section('title','Sign Up')
@section('content')
  <div class="auth-title">Create account</div>
  <p class="auth-sub">Join Issang Macchiatos and start pre-ordering.</p>

  <form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="row">
      <div class="form-group" style="flex:1">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
      </div>
      <div class="form-group" style="flex:1">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>
    <div class="form-group">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
    </div>
    <div class="form-group">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-group">
      <label class="form-label">Confirm Password</label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-primary btn-block">Create Account</button>
  </form>

  <p class="text-sm text-muted mt-16" style="text-align:center">
    Already have an account? <a href="{{ route('login') }}" style="color:var(--brand);font-weight:600">Sign in</a>
  </p>
@endsection