@extends('layouts.main')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <div class="dashboard-shell">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Users</p>
                <h1>All Users</h1>
            </div>
            <a href="{{ route('add') }}" class="primary-btn">Add User</a>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>Registered Accounts</h2>
            </div>

            <div class="subject-list">
                @forelse ($users as $user)
                    <div class="subject-item">
                        <div>
                            <h3>{{ $user->name }}</h3>
                            <p>{{ $user->email }} · {{ $user->role }}</p>
                        </div>
                        <span>{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="empty-state">No users found.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
