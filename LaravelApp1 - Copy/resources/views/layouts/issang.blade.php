<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Issang Macchiatos')</title>
    @vite('resources/css/issang.css')
</head>
<body>
    <div class="app-frame @yield('frameClass')">
        @hasSection('hideChrome')
        <main class="main-content @yield('mainClass')">
            @if (session('success'))
                <p class="flash-message" role="status">{{ session('success') }}</p>
            @endif
            @yield('content')
        </main>
        @else
        <header class="app-header">
            <a class="brand" href="{{ route('home') }}" aria-label="Issang Macchiatos home">
                <span class="brand-mark" aria-hidden="true">IM</span>
                <span class="brand-name">Issang Macchiatos</span>
            </a>

            @auth
                <nav class="main-nav" aria-label="Main navigation">
                    @if (in_array(strtolower(Auth::user()->role), ['admin', 'administrator'], true))
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
                        <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders') ? 'is-active' : '' }}">Orders</a>
                        <a href="{{ route('admin.inventory') }}" class="{{ request()->routeIs('admin.inventory*') ? 'is-active' : '' }}">Inventory</a>
                        <a href="{{ route('admin.sales') }}" class="{{ request()->routeIs('admin.sales*') ? 'is-active' : '' }}">Sales</a>
                        <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports*') ? 'is-active' : '' }}">Reports</a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'is-active' : '' }}">Dashboard</a>
                        <a href="{{ route('user.order.create') }}" class="{{ request()->routeIs('user.order.create') ? 'is-active' : '' }}">Place Orders</a>
                        <a href="{{ route('user.orders') }}" class="{{ request()->routeIs('user.orders') ? 'is-active' : '' }}">My Orders</a>
                    @endif
                </nav>

                <div class="header-actions">
                    <a class="notification-link" href="{{ route('notifications.index') }}" aria-label="Notifications">
                        <span class="bell-icon" aria-hidden="true"></span>
                    </a>
                    <span class="header-user">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Logout</button>
                    </form>
                </div>
            @else
                <nav class="guest-nav" aria-label="Account navigation">
                    <a href="{{ route('login') }}?portal=user">Customer Login</a>
                    <a href="{{ route('login') }}?portal=admin">Admin Login</a>
                    <a class="guest-register" href="{{ route('register') }}">Register</a>
                </nav>
            @endauth
        </header>

        <main class="main-content">
            @if (session('success'))
                <p class="flash-message" role="status">{{ session('success') }}</p>
            @endif
            @yield('content')
        </main>

        <footer class="app-footer">© {{ now()->year }} Issang Macchiatos <span>Pinaod, San Ildefonso, Bulacan</span></footer>
        @endif
    </div>
    @stack('scripts')
</body>
</html>