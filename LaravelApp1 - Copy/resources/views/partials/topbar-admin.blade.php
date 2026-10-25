<header class="topbar">
  <div class="flex center gap-12">
    <button class="mobile-toggle" data-toggle-sidebar>☰</button>
    <h1>@yield('page-title', 'Dashboard')</h1>
  </div>
  <div class="topbar-actions">
    <a href="{{ route('admin.notifications') }}" title="Notifications" style="position: relative; display: inline-flex; align-items: center; text-decoration: none;">
  <span style="font-size: 1.2rem;">🔔</span>
  @if(isset($unreadCount) && $unreadCount > 0)
    <span class="badge-count">{{ $unreadCount }}</span>
  @endif
</a>
    @if(auth('admin')->check())
  <div class="avatar">
    @if(auth('admin')->user()->avatar)
      <img src="{{ asset(auth('admin')->user()->avatar) }}" alt="Profile" class="avatar-img avatar-img-sm">
    @else
      {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
    @endif
  </div>
  <div>
    <div class="text-sm bold">{{ auth('admin')->user()->first_name }} {{ auth('admin')->user()->last_name }}</div>
    <div class="text-sm text-muted">{{ ucfirst(auth('admin')->user()->role) }}</div>
  </div>
@endif
</header>