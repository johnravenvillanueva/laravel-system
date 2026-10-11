<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index', ['announcements' => DemoData::announcements()]);
    }

    public function create()
    {
        return view('admin.announcements.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required','string','max:100'],
            'content' => ['required','string'],
            'target_audience' => ['required','in:all,customers,staff'],
            'is_active' => ['required','in:0,1'],
            'expires_at' => ['nullable','date'],
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement published.');
    }

    public function edit(int $id)
    {
        $announcement = DemoData::announcements()->firstWhere('id', $id) ?? DemoData::announcements()->first();
        return view('admin.announcements.form', ['announcement' => $announcement]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title' => ['required','string','max:100'],
            'content' => ['required','string'],
            'target_audience' => ['required','in:all,customers,staff'],
            'is_active' => ['required','in:0,1'],
            'expires_at' => ['nullable','date'],
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(int $id)
    {
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}