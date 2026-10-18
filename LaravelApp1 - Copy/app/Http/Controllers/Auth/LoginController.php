<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     *
     * Tries Admin table first. If no match, tries Customer table.
     * Both use `password_hash` column (not Laravel's default `password`),
     * so we manually verify instead of Auth::attempt().
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 1. Try Admin (covers both admin and staff)
        $admin = Admin::where('email', $credentials['email'])->first();

        if ($admin && Hash::check($credentials['password'], $admin->password_hash)) {
            if (! $admin->is_active) {
                return back()->withErrors(['email' => 'This account has been deactivated.']);
            }

            Auth::guard('admin')->login($admin, $request->boolean('remember'));
            $admin->update(['last_login' => now()]);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . $admin->first_name . '!');
        }

        // 2. Try Customer
        $customer = Customer::where('email', $credentials['email'])->first();

        if ($customer && $customer->password_hash
            && Hash::check($credentials['password'], $customer->password_hash)) {

            if (! $customer->is_active) {
                return back()->withErrors(['email' => 'This account has been deactivated.']);
            }

            Auth::guard('customer')->login($customer, $request->boolean('remember'));
            $customer->update(['last_login' => now()]);

            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back, ' . $customer->first_name . '!');
        }

        // Neither matched
        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    /**
     * Log out from whichever guard is active.
     */
    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        if (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}