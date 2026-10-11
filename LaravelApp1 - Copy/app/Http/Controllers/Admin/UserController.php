<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = DemoData::users();

        if ($q = $request->query('q')) {
            $users = $users->filter(fn($u) =>
                str_contains(strtolower($u->first_name . ' ' . $u->last_name . ' ' . $u->email), strtolower($q))
            );
        }
        if ($role = $request->query('role')) {
            $users = $users->where('role', $role);
        }

        return view('admin.users.index', ['users' => $users->values()]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:50'],
            'last_name'  => ['required','string','max:50'],
            'email'      => ['required','email','max:100'],
            'phone'      => ['nullable','string','max:15'],
            'role'       => ['required','in:staff,admin'], // ⚠️ customers NOT allowed
            'password'   => ['required','string','min:8','confirmed'],
        ]);

        // TODO: User::create($data) when DB is ready.

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff account created.');
    }

    public function edit(int $id)
    {
        $user = DemoData::users()->firstWhere('id', $id) ?? DemoData::users()->first();
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, int $id)
    {
        // Staff/admin accounts only — customers managed via self-service
        $data = $request->validate([
            'first_name' => ['required','string','max:50'],
            'last_name'  => ['required','string','max:50'],
            'email'      => ['required','email','max:100'],
            'phone'      => ['nullable','string','max:15'],
            'role'       => ['required','in:staff,admin'],
            'is_active'  => ['required','in:0,1'],
            'password'   => ['nullable','string','min:8'],
        ]);

        // TODO: update user when DB is ready.

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(int $id)
    {
        // TODO: delete when DB is ready.
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted.');
    }
}