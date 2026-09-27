<?php

namespace App\Http\Controllers;

use App\Models\Subjects;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subjects::with('users')->latest()->get();

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string|max:255',
            'time' => 'required|string|max:255',
            'room' => 'required|string|max:255',
        ]);

        Subjects::create($validated);

        return redirect()->route('subjects.index')->with('success', 'Subject added successfully.');
    }

    public function show(Subjects $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    public function edit(Subjects $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subjects $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'day' => 'required|string|max:255',
            'time' => 'required|string|max:255',
            'room' => 'required|string|max:255',
        ]);

        $subject->update($validated);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subjects $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }

    public function assignUser(Request $request, Subjects $subject)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $subject->users()->syncWithoutDetaching([$validated['user_id']]);

        return redirect()->route('subjects.index')->with('success', 'User assigned successfully.');
    }
}
