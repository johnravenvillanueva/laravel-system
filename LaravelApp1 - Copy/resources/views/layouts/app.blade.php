<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Issang Macchiatos')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
  @include('partials.navbar-public')
  <main class="container" style="padding-top:28px;padding-bottom:28px;min-height:70vh">
    @include('partials.flash')
    @yield('content')
  </main>
  @include('partials.footer')
  @stack('scripts')
</body>
</html>