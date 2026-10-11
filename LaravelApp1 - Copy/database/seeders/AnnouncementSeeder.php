<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('email', 'admin@issang.test')->first();

        // Active announcement — no expiry
        Announcement::create([
            'created_by'      => $admin->admin_id,
            'title'           => 'Welcome to Issang Macchiatos Online!',
            'content'         => 'You can now pre-order your favorite drinks and pastries online. Skip the line and pick up at your chosen time.',
            'target_audience' => 'all',
            'is_active'       => true,
            'expires_at'      => null,
        ]);

        // Holiday closure — expires in 7 days
        Announcement::create([
            'created_by'      => $admin->admin_id,
            'title'           => 'Holiday Hours',
            'content'         => 'We will be closing early at 6:00 PM on December 24 and will be closed on December 25. Please plan your pre-orders accordingly.',
            'target_audience' => 'all',
            'is_active'       => true,
            'expires_at'      => now()->addDays(7),
        ]);

        // Customer-only promo
        Announcement::create([
            'created_by'      => $admin->admin_id,
            'title'           => 'Buy 1 Get 1 on Spanish Latte!',
            'content'         => 'Every Friday this month, buy one Spanish Latte and get another one free. Available for pre-orders only.',
            'target_audience' => 'customers',
            'is_active'       => true,
            'expires_at'      => now()->addDays(30),
        ]);

        // Staff-only internal notice
        Announcement::create([
            'created_by'      => $admin->admin_id,
            'title'           => 'Team Meeting — Saturday 8 AM',
            'content'         => 'Reminder: we have our monthly team meeting this Saturday at 8:00 AM before opening. Please come 15 minutes early.',
            'target_audience' => 'staff',
            'is_active'       => true,
            'expires_at'      => now()->addDays(5),
        ]);

        // Expired announcement (should not show on landing)
        Announcement::create([
            'created_by'      => $admin->admin_id,
            'title'           => 'Grand Opening Special',
            'content'         => 'Thank you for joining us during our grand opening! Enjoy 20% off all drinks this week.',
            'target_audience' => 'all',
            'is_active'       => true,
            'expires_at'      => now()->subDays(10),  // already expired
        ]);

        // Inactive announcement (admin turned it off)
        Announcement::create([
            'created_by'      => $admin->admin_id,
            'title'           => 'Old News',
            'content'         => 'This was a previous announcement that has been deactivated.',
            'target_audience' => 'all',
            'is_active'       => false,
            'expires_at'      => null,
        ]);
    }
}