@extends('layouts.issang')

@section('title', 'Notifications | Issang Macchiatos')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">{{ $isAdmin ? 'Operations updates' : 'Pickup updates and cafe news' }}</p><h1>Notifications</h1><p class="page-subtitle">Stay up to date with Issang Macchiatos.</p></div>
        <button class="quiet-button" type="button" id="mark-all-read">Mark all as read</button>
    </div>
    <div class="notification-list">
        @foreach ($items as $index => $item)
            <article class="notification-card {{ $index === 0 ? 'is-unread' : '' }}" data-notification>
                <span class="notification-dot tone-{{ $item['tone'] }}" aria-hidden="true"></span>
                <div><h2>{{ $item['title'] }}</h2><p>{{ $item['details'] }}</p><time>{{ $item['time'] }}</time></div>
                <button type="button" class="text-button mark-read" aria-label="Mark {{ $item['title'] }} as read">Mark read</button>
            </article>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.mark-read').forEach((button) => button.addEventListener('click', () => button.closest('[data-notification]').classList.remove('is-unread')));
document.getElementById('mark-all-read').addEventListener('click', () => document.querySelectorAll('[data-notification]').forEach((item) => item.classList.remove('is-unread')));
</script>
@endpush