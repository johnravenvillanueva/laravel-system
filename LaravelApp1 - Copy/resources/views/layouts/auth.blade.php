<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#1b120c">
  <title>@yield('title', 'Sign In') · Issang Macchiatos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body data-layout="auth">
  <main class="auth-wrap">
    <div class="auth-card">
      <a href="{{ route('home') }}" class="auth-logo" aria-label="Issang Macchiatos home">
        <img src="{{ asset('images/products/isanglogoreal.jpg') }}" alt="" width="64" height="64">
        <span>Issang Macchiatos</span>
      </a>
      @include('partials.flash')
      @yield('content')
    </div>
    <a href="{{ route('home') }}" class="auth-back">@include('partials.icon', ['ico' => 'arrow-right']) Back to home</a>
  </main>
  @stack('scripts')
</body>
</html>
