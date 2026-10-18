<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{

    public function index()
    {
        $announcements = Announcement::with('author')
            ->latest()
            ->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:100'],
            'content'         => ['required', 'string'],
            'target_audience' => ['required', 'in:all,customers,staff'],
            'is_active'       => ['required', 'in:0,1'],
            'expires_at'      => ['nullable', 'date', 'after:now'],
        ]);

        $announcement = DB::transaction(function () use ($data) {

            $announcement = Announcement::create([
                'created_by'      => Auth::guard('admin')->id(),
                'title'           => $data['title'],
                'content'         => $data['content'],
                'target_audience' => $data['target_audience'],
                'is_active'       => (bool) $data['is_active'],
                'expires_at'      => $data['expires_at'] ?? null,
            ]);

            // Fan out notifications to the target audience
            if ($announcement->is_active) {
                $this->fanOutNotifications($announcement);
            }

            return $announcement;
        });

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement published.');
    }

    //edit update
    public function edit(int $id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.form', compact('announcement'));
    }

    public function update(Request $request, int $id)
    {
        $announcement = Announcement::findOrFail($id);

        $data = $request->validate([
            'title'           => ['required', 'string', 'max:100'],
            'content'         => ['required', 'string'],
            'target_audience' => ['required', 'in:all,customers,staff'],
            'is_active'       => ['required', 'in:0,1'],
            'expires_at'      => ['nullable', 'date'],
        ]);

        $wasInactive = ! $announcement->is_active;

        $announcement->update([
            'title'           => $data['title'],
            'content'         => $data['content'],
            'target_audience' => $data['target_audience'],
            'is_active'       => (bool) $data['is_active'],
            'expires_at'      => $data['expires_at'] ?? null,
        ]);

        // If it just became active (was inactive), fan out now
        if ($wasInactive && $announcement->is_active) {
            $this->fanOutNotifications($announcement);
        }

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated.');
    }

        //delete

    public function destroy(int $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    protected function fanOutNotifications(Announcement $announcement): void
    {
        // 1. Send to customers
        if (in_array($announcement->target_audience, ['all', 'customers'])) {
            $customers = Customer::where('is_active', true)->get();

            foreach ($customers as $customer) {
                Notification::create([
                    'customer_id'     => $customer->customer_id,
                    'announcement_id' => $announcement->announcement_id,
                    'title'           => $announcement->title,
                    'message'         => $announcement->content,
                    'type'            => 'announcement',
                    'is_read'         => false,
                ]);
            }
        }

        // 2. Send to admins + staff (both live in `admins`)
        if (in_array($announcement->target_audience, ['all', 'staff'])) {
            $admins = \App\Models\Admin::where('is_active', true)->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'admin_id'        => $admin->admin_id,
                    'announcement_id' => $announcement->announcement_id,
                    'title'           => $announcement->title,
                    'message'         => $announcement->content,
                    'type'            => 'announcement',
                    'is_read'         => false,
                ]);
            }
        }
    }
}