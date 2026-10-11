<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        $user = (object)[
            'first_name' => 'Admin', 'last_name' => 'User',
            'email' => 'admin@issang.test', 'phone' => '',
            'role' => 'admin',
        ];
        return view('admin.profile', ['user' => $user]);
    }

    public function update()
    {
        return back()->with('success', 'Profile updated.');
    }

    public function password()
    {
        return back()->with('success', 'Password changed.');
    }
}