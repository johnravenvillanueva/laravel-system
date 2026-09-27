@extends('layouts.issang')

@section('title', ($portal === 'admin' ? 'Admin Login' : 'Customer Login').' | Issang Macchiatos')
@section('hideChrome', 'true')
@section('frameClass', 'auth-frame')
@section('mainClass', 'auth-content')
@section('content')
    <section class="auth-card">
        <a class="auth-brand" href="{{ route('home') }}" aria-label="Issang Macchiatos home"><span class="brand-mark" aria-hidden="true">IM</span><span>Issang Macchiatos</span></a>
        <p class="auth-portal-label">USER LOGIN <span>|</span> CUSTOMER LOGIN</p>
        <div class="auth-panel">
            <p class="eyebrow">{{ $portal === 'admin' ? 'ADMIN ACCESS' : 'CUSTOMER ACCESS' }}</p>
            <h1>{{ $portal === 'admin' ? 'ADMIN LOGIN' : 'USER LOGIN' }}</h1>
            <p class="auth-description">{{ $portal === 'admin' ? 'Sign in to manage orders, sales, inventory and reports.' : 'Sign in to order your favorites and follow their pickup status.' }}</p>

            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif

            <form class="stack-form" method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="portal" value="{{ $portal }}">
                <label class="form-field">
                    <span>EMAIL</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" autocomplete="email" required autofocus>
                </label>
                <label class="form-field">
                    <span>PASSWORD</span>
                    <input type="password" name="password" placeholder="Enter password" autocomplete="current-password" required>
                </label>
                <div class="form-options">
                    <label class="check-field"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> <span>Remember me</span></label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
                <button class="primary-button auth-submit" type="submit">LOGIN</button>
            </form>

            @if ($portal === 'admin')
                <p class="auth-switch"><a href="{{ route('login') }}?portal=user">USER LOGIN</a> <span>|</span> <a href="{{ route('login') }}?portal=user">CUSTOMER LOGIN</a></p>
            @else
                <p class="auth-switch">No account yet? <a href="{{ route('register') }}">REGISTER</a></p>
                <p class="auth-switch"><a href="{{ route('login') }}?portal=admin">ADMIN LOGIN</a></p>
            @endif
        </div>
    </section>
@endsection
