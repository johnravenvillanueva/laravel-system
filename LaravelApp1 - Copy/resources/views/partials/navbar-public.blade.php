<nav class="navbar">
  <div class="navbar-inner">
    <a href="{{ route('home') }}" class="logo">Issang Macchiatos</a>

    <button class="mobile-toggle" data-toggle-nav>☰</button>

    <div class="nav-links">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
      <a href="{{ route('menu') }}" class="{{ request()->routeIs('menu*') ? 'active' : '' }}">Menu</a>
      @auth
        @if(auth()->user()->role === 'customer')
          <a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders*') ? 'active' : '' }}">My Orders</a>
        @endif
      @endauth
    </div>

    <div class="nav-actions">
      @auth
        @if(auth()->user()->role === 'customer')
          <a href="{{ route('customer.cart') }}" class="btn btn-outline btn-sm">🛒 Cart</a>
          <a href="{{ route('customer.notifications') }}" class="btn btn-outline btn-sm">🔔</a>
          <a href="{{ route('customer.profile') }}" class="avatar" title="{{ auth()->user()->first_name }}">
            {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
          </a>
          <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button class="btn btn-outline btn-sm">Logout</button>
          </form>
        @else
          <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
        @endif
      @else
        <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Login</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
      @endauth
    </div>
  </div>
</nav>