<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Handle customer self-registration.
     *
     * IMPORTANT: The role is HARD-CODED to 'customer'.
     * Admin/staff accounts are created ONLY via /admin/users/create by an admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => [
                'required',
                'email',
                'max:100',
                Rule::unique('customers', 'email'),
                Rule::unique('admins', 'email'),
            ],
            'phone'      => ['nullable', 'string', 'max:15'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'This email is already registered.',
        ]);

        // Create the customer — role is always 'customer' (never trusted from form)
        $customer = Customer::create([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'is_guest'      => false,
            'is_active'     => true,
        ]);

        // Log them in immediately
        Auth::guard('customer')->login($customer);

        return redirect()->route('home')
            ->with('success', 'Welcome to Issang Macchiatos, ' . $customer->first_name . '!');
    }
}