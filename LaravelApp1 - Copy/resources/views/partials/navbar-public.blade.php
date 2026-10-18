<nav class="navbar">
  <div class="navbar-inner">
    <a href="{{ route('home') }}" class="logo">Issang Macchiatos</a>

    <button class="mobile-toggle" data-toggle-nav>☰</button>

    <div class="nav-links">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
      <a href="{{ route('menu') }}" class="{{ request()->routeIs('menu*') ? 'active' : '' }}">Menu</a>

      @if(auth('customer')->check())
        <a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders*') ? 'active' : '' }}">My Orders</a>
      @endif
    </div>

    <div class="nav-actions">
      @if(auth('admin')->check())
        {{-- Admin / Staff logged in --}}
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
        <div class="avatar" title="{{ auth('admin')->user()->first_name }}">
          {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
          @csrf
          <button class="btn btn-outline btn-sm">Logout</button>
        </form>

      @elseif(auth('customer')->check())
        {{-- Customer logged in --}}
        <a href="{{ route('customer.cart') }}" class="btn btn-outline btn-sm">🛒 Cart</a>
        <a href="{{ route('customer.notifications') }}" class="btn btn-outline btn-sm">🔔</a>
        <a href="{{ route('customer.profile') }}" class="avatar" title="{{ auth('customer')->user()->first_name }}">
          {{ strtoupper(substr(auth('customer')->user()->first_name, 0, 1)) }}
        </a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
          @csrf
          <button class="btn btn-outline btn-sm">Logout</button>
        </form>

      @else
        {{-- Guest --}}
        <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Login</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
      @endif
    </div>
  </div>
</nav>