<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#1b120c">
  <title>@yield('title', 'Dashboard') · Issang Macchiatos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body data-layout="admin">
<a href="#main" class="skip-link">Skip to content</a>
<div class="admin-layout">
  @include('partials.sidebar-admin')
  <div class="admin-main">
    @include('partials.topbar-admin')
    <main id="main" class="admin-content">
      @include('partials.flash')
      @yield('content')
    </main>
  </div>
</div>
@stack('scripts')
</body>
</html>
