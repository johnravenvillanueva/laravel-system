<aside class="sidebar" aria-label="Admin navigation">
  <div class="sidebar-brand">
    <img src="{{ asset('images/products/isanglogoreal.jpg') }}" alt="" width="44" height="44">
    <div>
      <div class="sidebar-brand-name">Issang</div>
      <div class="sidebar-brand-sub">Macchiatos admin</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'dashboard']) Dashboard</a>

    <div class="nav-section">Sales &amp; orders</div>
    <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') && ! request()->routeIs('admin.sales.create') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'receipt']) Sales</a>
    <a href="{{ route('admin.sales.create') }}" class="{{ request()->routeIs('admin.sales.create') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'plus-circle']) New walk-in sale</a>
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'package']) Orders</a>

    <div class="nav-section">Catalog</div>
    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'coffee']) Products</a>
    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'tag']) Categories</a>

    <div class="nav-section">Inventory</div>
    <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.*') && ! request()->routeIs('admin.inventory.transactions') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'archive']) Inventory</a>
    <a href="{{ route('admin.inventory.transactions') }}" class="{{ request()->routeIs('admin.inventory.transactions') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'repeat']) Transactions</a>

    <div class="nav-section">Communication</div>
    <a href="{{ route('admin.announcements.index') }}" class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'megaphone']) Announcements</a>

    <div class="nav-section">Reports</div>
    <a href="{{ route('admin.reports.sales') }}" class="{{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'trending-up']) Sales report</a>
    <a href="{{ route('admin.reports.inventory') }}" class="{{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'trending-down']) Inventory report</a>

    @if(auth('admin')->check() && auth('admin')->user()->role === 'admin')
      <div class="nav-section">Admin</div>
      <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'users']) Staff &amp; users</a>
    @endif

    <div class="nav-section">Account</div>
    <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">@include('partials.icon', ['ico' => 'user']) Profile</a>
    <form action="{{ route('logout') }}" method="POST" class="logout-form">
      @csrf
      <button type="submit" class="btn-logout">@include('partials.icon', ['ico' => 'log-out']) Log out</button>
    </form>
  </nav>
</aside>
