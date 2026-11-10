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
  <div class="auth-form-back">
    <a href="{{ route('login') }}" class="back-link back-link--dark">@include('partials.icon', ['ico' => 'arrow-left'])<span>Back to login</span></a>
  </div>
@endsection