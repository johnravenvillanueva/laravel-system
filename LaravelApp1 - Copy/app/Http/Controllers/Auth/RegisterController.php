<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:50'],
            'last_name'  => ['required','string','max:50'],
            'email'      => ['required','email','max:100'],
            'phone'      => ['nullable','string','max:15'],
            'password'   => ['required','string','min:8','confirmed'],
        ]);

        // TODO: replace with User::create([...]) when DB is ready.
        // NOTE: 'role' is HARD-CODED here — never accepted from the form.
        // Admin/staff accounts are created ONLY via /admin/users/create.
        $data['role'] = 'customer';

        return redirect()->route('login')
            ->with('success', 'Account created. Please sign in.');
    }
}