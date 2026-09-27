@extends('layouts.main')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <div class="dashboard-shell">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Management</p>
                <h1>Subjects</h1>
            </div>
            <a href="{{ route('subjects.create') }}" class="primary-btn">Add Subject</a>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>All Subjects</h2>
            </div>

            <div class="subject-list">
                @forelse ($subjects as $subject)
                    <div class="subject-item">
                        <div>
                            <h3>{{ $subject->name }}</h3>
                            <p>{{ $subject->day }} · {{ $subject->time }}</p>
                        </div>
                        <div class="subject-actions">
                            <a href="{{ route('subjects.show', $subject) }}">View</a>
                            <a href="{{ route('subjects.edit', $subject) }}">Edit</a>
                            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Delete this subject?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">No subjects found.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
