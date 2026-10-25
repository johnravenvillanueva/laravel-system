@extends('layouts.admin')
@section('title','Notifications')
@section('page-title','Notifications')
@section('content')
  <div class="flex between mb-16">
    <h3 class="text-lg bold">Your Notifications</h3>
    <form method="POST" action="{{ route('admin.notifications.read-all') }}">
      @csrf
      <button class="btn btn-outline btn-sm">Mark all as read</button>
    </form>
  </div>

  <div class="card">
    @forelse($notifications as $n)
      <div class="notif-item {{ $n->is_read ? '' : 'unread' }}">
        @if(!$n->is_read)<div class="notif-dot"></div>@endif
        <div style="flex:1">
          <div class="bold">{{ $n->title }}</div>
          <div class="text-sm text-muted">{{ $n->message }}</div>
          <div class="text-sm text-muted mt-8">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @if($n->order_id)
          <a href="{{ route('admin.orders.show', $n->order_id) }}" class="btn btn-outline btn-sm">View Order</a>
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