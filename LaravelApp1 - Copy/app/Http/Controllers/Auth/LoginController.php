<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // TODO: Auth::attempt() when DB is ready.
        // For now: fake role-based redirect for front-end demo.
        $role = str_contains($request->email, 'admin') ? 'admin' : 'customer';

        return $role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }

    public function logout(Request $request)
    {
        // TODO: Auth::logout() + session invalidate when DB is ready.
        return redirect()->route('home');
    }
}