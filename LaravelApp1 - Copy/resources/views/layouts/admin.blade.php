<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') · Issang Macchiatos</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<div class="admin-layout">
  @include('partials.sidebar-admin')
  <div class="admin-main">
    @include('partials.topbar-admin')
    <div class="admin-content">
      @include('partials.flash')
      @yield('content')
    </div>
  </div>
</div>
@stack('scripts')
</body>
</html>