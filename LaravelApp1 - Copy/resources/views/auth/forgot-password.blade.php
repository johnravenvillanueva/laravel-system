@extends('layouts.auth')
@section('title','Forgot Password')
@section('content')
  <div class="auth-title">Forgot password?</div>
  <p class="auth-sub">Enter your email and we'll send you a reset link.</p>

  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <button class="btn btn-primary btn-block">Send Reset Link</button>
  </form>
  <p class="text-sm text-muted mt-16" style="text-align:center">
    <a href="{{ route('login') }}" style="color:var(--brand)">← Back to login</a>
  </p>
@endsection