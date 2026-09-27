@extends('layouts.main')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <div class="dashboard-shell">
        <div class="panel">
            <div class="panel-header">
                <h2>{{ $subject->name }}</h2>
            </div>

            <div class="subject-meta">
                <p><strong>Day:</strong> {{ $subject->day }}</p>
                <p><strong>Time:</strong> {{ $subject->time }}</p>
                <p><strong>Room:</strong> {{ $subject->room }}</p>
            </div>

            <a href="{{ route('subjects.index') }}" class="primary-btn">Back to Subjects</a>
        </div>
    </div>
@endsection
