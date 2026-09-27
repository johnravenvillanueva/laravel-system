@extends('layouts.issang')

@section('title', 'Create an Account | Issang Macchiatos')
@section('hideChrome', 'true')
@section('frameClass', 'auth-frame')
@section('mainClass', 'auth-content')
@section('content')
    <section class="auth-card auth-card-register">
        <a class="auth-brand" href="{{ route('home') }}" aria-label="Issang Macchiatos home"><span class="brand-mark" aria-hidden="true">IM</span><span>Issang Macchiatos</span></a>
        <p class="auth-portal-label">USER LOGIN <span>|</span> CUSTOMER LOGIN</p>
        <div class="auth-panel">
            <p class="eyebrow">CUSTOMER ACCOUNT</p>
            <h1>REGISTER</h1>
            <p class="auth-description">Create your customer account.</p>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <form class="stack-form register-form" method="POST" action="{{ route('register') }}">
                @csrf
                <label class="form-field"><span>FULL NAME</span><input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name" autocomplete="name" required></label>
                <label class="form-field"><span>EMAIL ADDRESS</span><input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" autocomplete="email" required></label>
                <label class="form-field"><span>PHONE NUMBER</span><input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number" autocomplete="tel" required></label>
                <label class="form-field"><span>ADDRESS</span><input type="text" name="address" value="{{ old('address') }}" placeholder="Enter address" autocomplete="street-address" required></label>
                <label class="form-field"><span>PASSWORD</span><input type="password" name="password" placeholder="Enter password" autocomplete="new-password" required></label>
                <label class="form-field"><span>CONFIRM PASSWORD</span><input type="password" name="password_confirmation" placeholder="Confirm your password" autocomplete="new-password" required></label>
                <button class="primary-button auth-submit" type="submit">REGISTER</button>
            </form>
            <p class="auth-switch">Already registered? <a href="{{ route('login') }}?portal=user">USER LOGIN</a> <span>|</span> CUSTOMER LOGIN</p>
        </div>
    </section>
@endsection
