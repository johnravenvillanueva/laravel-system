<header class="navbar">
  <div class="navbar-inner">
    <a href="{{ route('home') }}" class="logo" aria-label="Issang Macchiatos home">
      <img src="{{ asset('images/products/isanglogoreal.jpg') }}" alt="" width="40" height="40">
      <span class="logo-text">Issang Macchiatos</span>
    </a>

    <nav class="nav-links" aria-label="Main">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
      <a href="{{ route('menu') }}" class="{{ request()->routeIs('menu*') ? 'active' : '' }}" @if(request()->routeIs('menu*')) aria-current="page" @endif>Menu</a>

      @if(auth('customer')->check())
        <a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders*') ? 'active' : '' }}">My Orders</a>
      @endif
    </nav>

    <div class="nav-actions">
      @if(auth('admin')->check())
        {{-- Admin / Staff logged in --}}
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
        <div class="avatar" title="{{ auth('admin')->user()->first_name }}">
          {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
        </div>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
          @csrf
          <button class="icon-btn" aria-label="Log out" title="Log out">@include('partials.icon', ['ico' => 'log-out'])</button>
        </form>

      @elseif(auth('customer')->check())
        {{-- Customer logged in --}}
        <a href="{{ route('customer.cart') }}" class="icon-btn" aria-label="Cart" title="Cart">@include('partials.icon', ['ico' => 'cart'])</a>
        <a href="{{ route('customer.notifications') }}" class="icon-btn" aria-label="Notifications" title="Notifications">@include('partials.icon', ['ico' => 'bell'])</a>
        <a href="{{ route('customer.profile') }}" title="{{ auth('customer')->user()->first_name }}" aria-label="My profile">
          @if(auth('customer')->user()->avatar)
            <img src="{{ asset(auth('customer')->user()->avatar) }}" alt="" class="avatar-img avatar-img-sm">
          @else
            <div class="avatar">{{ strtoupper(substr(auth('customer')->user()->first_name, 0, 1)) }}</div>
          @endif
        </a>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
          @csrf
          <button class="icon-btn" aria-label="Log out" title="Log out">@include('partials.icon', ['ico' => 'log-out'])</button>
        </form>

      @else
        {{-- Guest --}}
        <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Login</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm hide-xs">Sign Up</a>
      @endif

      <button type="button" class="mobile-toggle" data-toggle-nav aria-label="Menu">@include('partials.icon', ['ico' => 'menu'])</button>
    </div>
  </div>
</header>
