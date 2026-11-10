@extends('layouts.app')
@section('title','Notifications')
@section('content')
  <div class="flex between mb-16">
    <h2 class="text-2xl bold">Notifications</h2>
    <form method="POST" action="{{ route('customer.notifications.read-all') }}">
      @csrf
      <button class="btn btn-outline btn-sm">Mark all as read</button>
    </form>
  </div>

  <div class="card">
    @forelse($notifications ?? [] as $n)
      <div class="notif-item {{ $n->is_read ? '' : 'unread' }}">
        @if(!$n->is_read)<div class="notif-dot"></div>@endif
        <form method="POST" action="{{ route('customer.notifications.open', $n->notification_id) }}" class="notif-open-form">
          @csrf
          <button type="submit" class="notif-content notif-content-button">
            <span class="bold">{{ $n->title }}</span>
            <span class="text-sm text-muted">{{ $n->message }}</span>
            <span class="text-sm text-muted mt-8">{{ $n->created_at->diffForHumans() }}</span>
          </button>
        </form>
        @if($n->order_id)
          <form method="POST" action="{{ route('customer.notifications.open', $n->notification_id) }}">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">View Order</button>
          </form>
        @endif
      </div>
    @empty
      <div class="card-body" style="text-align:center;padding:48px">
        <div style="font-size:3rem">🔔</div>
        <p class="text-muted mt-16">No notifications yet.</p>
      </div>
    @endforelse
  </div>
@endsection