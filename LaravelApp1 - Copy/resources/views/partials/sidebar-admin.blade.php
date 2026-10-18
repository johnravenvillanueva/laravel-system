<aside class="sidebar">
  <div class="sidebar-brand">
  <img src="{{ asset('images/isanglogoreal.jpg') }}" alt="Logo" style="height:32px;width:auto;margin-right:8px;">
</div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>

    <div class="nav-section">Sales & Orders</div>
    <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">🧾 Sales</a>
    <a href="{{ route('admin.sales.create') }}" class="{{ request()->routeIs('admin.sales.create') ? 'active' : '' }}">➕ New Walk-in Sale</a>
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">📦 Orders</a>

    <div class="nav-section">Catalog</div>
    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">☕ Products</a>
    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">🏷️ Categories</a>

    <div class="nav-section">Inventory</div>
    <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">📦 Inventory</a>
    <a href="{{ route('admin.inventory.transactions') }}" class="{{ request()->routeIs('admin.inventory.transactions') ? 'active' : '' }}">🔁 Transactions</a>

    <div class="nav-section">Communication</div>
    <a href="{{ route('admin.announcements.index') }}" class="{{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">📢 Announcements</a>

    <div class="nav-section">Reports</div>
    <a href="{{ route('admin.reports.sales') }}" class="{{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">📈 Sales Report</a>
    <a href="{{ route('admin.reports.inventory') }}" class="{{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">📉 Inventory Report</a>

    @if(auth('admin')->check() && auth('admin')->user()->role === 'admin')
      <div class="nav-section">Admin</div>
      <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👥 Staff & Users</a>
    @endif

    <div class="nav-section">Account</div>
    <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">👤 Profile</a>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button style="width:100%;text-align:left;display:flex;gap:12px;padding:11px 14px;color:#c9b8a8;font-size:.9rem;background:none;border:none;cursor:pointer">🚪 Logout</button>
    </form>
  </nav>
</aside>