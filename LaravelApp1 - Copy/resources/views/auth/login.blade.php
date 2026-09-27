@extends('layouts.auth')
@section('title','Sign In')
@section('content')
  <div class="auth-title">Welcome back ☕</div>
  <p class="auth-sub">Sign in to your Issang Macchiatos account.</p>

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="form-group">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="flex between mb-16">
      <label class="text-sm"><input type="checkbox" name="remember"> Remember me</label>
      <a href="{{ route('password.request') }}" class="text-sm" style="color:var(--brand)">Forgot password?</a>
    </div>
    <button class="btn btn-primary btn-block">Sign In</button>
  </form>

  <p class="text-sm text-muted mt-16" style="text-align:center">
    Don't have an account? <a href="{{ route('register') }}" style="color:var(--brand);font-weight:600">Sign up</a>
  </p>
@endsection