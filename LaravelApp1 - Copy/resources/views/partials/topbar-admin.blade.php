<header class="topbar">
  <div class="flex center gap-12">
    <button class="mobile-toggle" data-toggle-sidebar>☰</button>
    <h1>@yield('page-title', 'Dashboard')</h1>
  </div>
  <div class="topbar-actions">
    <a href="{{ route('admin.announcements.index') }}" title="Notifications">🔔</a>
    <div class="avatar">{{ strtoupper(substr(auth()->user()->first_name ?? 'U',0,1)) }}</div>
    <div>
      <div class="text-sm bold">{{ auth()->user()->first_name ?? 'User' }} {{ auth()->user()->last_name ?? '' }}</div>
      <div class="text-sm text-muted">{{ ucfirst(auth()->user()->role ?? 'guest') }}</div>
    </div>
  </div>
</header>