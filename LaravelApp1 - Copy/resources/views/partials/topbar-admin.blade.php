<header class="topbar">
  <div class="flex center gap-12">
    <button type="button" class="mobile-toggle" data-toggle-sidebar aria-label="Open menu">@include('partials.icon', ['ico' => 'menu'])</button>
    <h1>@yield('page-title', 'Dashboard')</h1>
  </div>
  <div class="topbar-actions">
    <span class="topbar-date">{{ now()->format('D, M j') }}</span>
    <a href="{{ route('admin.notifications') }}" class="icon-btn" title="Notifications" aria-label="Notifications{{ isset($unreadCount) && $unreadCount > 0 ? ' (' . $unreadCount . ' unread)' : '' }}">
      @include('partials.icon', ['ico' => 'bell'])
      @if(isset($unreadCount) && $unreadCount > 0)
        <span class="badge-count">{{ $unreadCount }}</span>
      @endif
    </a>
    @if(auth('admin')->check())
      <div class="avatar">
        @if(auth('admin')->user()->avatar)
          <img src="{{ asset(auth('admin')->user()->avatar) }}" alt="" class="avatar-img avatar-img-sm">
        @else
          {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
        @endif
      </div>
      <div class="topbar-user">
        <div class="text-sm bold">{{ auth('admin')->user()->first_name }} {{ auth('admin')->user()->last_name }}</div>
        <div class="text-sm text-muted">{{ ucfirst(auth('admin')->user()->role) }}</div>
      </div>
    @endif
  </div>
</header>
