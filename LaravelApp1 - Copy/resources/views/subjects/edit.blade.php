@extends('layouts.main')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <div class="dashboard-shell">
        <div class="panel">
            <div class="panel-header">
                <h2>Edit Subject</h2>
            </div>

            <form action="{{ route('subjects.update', $subject) }}" method="POST" class="stack-form">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label for="name">Subject Name</label>
                    <input type="text" id="name" name="name" value="{{ $subject->name }}" required>
                </div>

                <div class="field-group">
                    <label for="day">Day</label>
                    <input type="text" id="day" name="day" value="{{ $subject->day }}" required>
                </div>

                <div class="field-group">
                    <label for="time">Time</label>
                    <input type="text" id="time" name="time" value="{{ $subject->time }}" required>
                </div>

                <div class="field-group">
                    <label for="room">Room</label>
                    <input type="text" id="room" name="room" value="{{ $subject->room }}" required>
                </div>

                <button type="submit" class="primary-btn">Update Subject</button>
            </form>
        </div>
    </div>
@endsection
