<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    public function show()
    {
        $user = Auth::guard('admin')->user();
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => [
                'required', 'email', 'max:100',
                Rule::unique('admins', 'email')->ignore($admin->admin_id, 'admin_id'),
                Rule::unique('customers', 'email'),
            ],
            'phone'      => ['nullable', 'string', 'max:15'],
            'avatar'     => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($admin->avatar && Storage::disk('public')->exists(str_replace('storage/', '', $admin->avatar))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $admin->avatar));
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = 'storage/' . $path;
        }

        $admin->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function password(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $admin->password_hash)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update([
            'password_hash' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}