<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'all');
        $q = $request->query('q');

        // Staff/admins live in `admins`
        $admins = Admin::query();
        if ($q) {
            $admins->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }
        if ($role === 'admin') {
            $admins->where('role', 'admin');
        } elseif ($role === 'staff') {
            $admins->where('role', 'staff');
        } elseif ($role === 'customer') {
            // Hide staff entirely when filtering customers
            $admins->whereRaw('1=0');
        }
        $admins = $admins->latest()->get();

        // Customers live in `customers`
        $customers = Customer::query();
        if ($q) {
            $customers->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            });
        }
        if ($role === 'admin' || $role === 'staff') {
            // Hide customers entirely when filtering staff
            $customers->whereRaw('1=0');
        }
        if ($role === 'customer') {
            $customers->where('is_guest', false);
        }
        $customers = $customers->latest()->get();

        // Merge into one list the Blade can iterate (with a `type` flag)
        $users = collect()
            ->merge($admins->map(fn($u) => (object) [
                'id'         => $u->admin_id,
                'first_name' => $u->first_name,
                'last_name'  => $u->last_name,
                'email'      => $u->email,
                'phone'      => $u->phone,
                'role'       => $u->role,
                'is_active'  => $u->is_active,
                'type'       => 'admin',
            ]))
            ->merge($customers->map(fn($u) => (object) [
                'id'         => $u->customer_id,
                'first_name' => $u->first_name,
                'last_name'  => $u->last_name,
                'email'      => $u->email,
                'phone'      => $u->phone,
                'role'       => $u->is_guest ? 'guest' : 'customer',
                'is_active'  => $u->is_active,
                'type'       => 'customer',
            ]));

        return view('admin.users.index', compact('users', 'role', 'q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => [
                'required', 'email', 'max:100',
                Rule::unique('admins', 'email'),
                Rule::unique('customers', 'email'),
            ],
            'phone'      => ['nullable', 'string', 'max:15'],
            'role'       => ['required', 'in:staff,admin'], // customers NOT allowed
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Admin::create([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'role'          => $data['role'],
            'password_hash' => Hash::make($data['password']),
            'is_active'     => true,
            'created_by'    => Auth::guard('admin')->id(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Staff account created.');
    }

    public function edit(int $id)
    {
        // Try admin first, then customer
        $admin = Admin::find($id);
        if ($admin) {
            return view('admin.users.edit', ['user' => $admin, 'type' => 'admin']);
        }

        $customer = Customer::findOrFail($id);
        return view('admin.users.edit', ['user' => $customer, 'type' => 'customer']);
    }

    public function update(Request $request, int $id)
    {
        // Detect which type we're editing — trust the hidden field if present
        $type = $request->input('type', 'admin');

        if ($type === 'admin') {
            return $this->updateAdmin($request, $id);
        }

        return $this->updateCustomer($request, $id);
    }

    protected function updateAdmin(Request $request, int $id)
    {
        $admin = Admin::findOrFail($id);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => [
                'required', 'email', 'max:100',
                Rule::unique('admins', 'email')->ignore($admin->admin_id, 'admin_id'),
                Rule::unique('customers', 'email'),
            ],
            'phone'      => ['nullable', 'string', 'max:15'],
            'role'       => ['required', 'in:staff,admin'],
            'is_active'  => ['required', 'in:0,1'],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Don't let an admin demote themselves
        if ($admin->admin_id === Auth::guard('admin')->id()
            && $data['role'] !== 'admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        // Don't let anyone deactivate themselves
        if ($admin->admin_id === Auth::guard('admin')->id() && ! (bool) $data['is_active']) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $admin->fill([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'role'       => $data['role'],
            'is_active'  => (bool) $data['is_active'],
        ]);

        if (! empty($data['password'])) {
            $admin->password_hash = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    protected function updateCustomer(Request $request, int $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => [
                'nullable', 'email', 'max:100',
                Rule::unique('customers', 'email')->ignore($customer->customer_id, 'customer_id'),
                Rule::unique('admins', 'email'),
            ],
            'phone'      => ['nullable', 'string', 'max:15'],
            'is_active'  => ['required', 'in:0,1'],
        ]);

        $customer->update([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'] ?? null,
            'phone'      => $data['phone'] ?? null,
            'is_active'  => (bool) $data['is_active'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Customer updated.');
    }

    public function destroy(int $id)
    {
        // Try admin first
        $admin = Admin::find($id);
        if ($admin) {
            // Can't delete yourself
            if ($admin->admin_id === Auth::guard('admin')->id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            // Can't delete the last remaining admin
            if ($admin->role === 'admin' && Admin::where('role', 'admin')->count() <= 1) {
                return back()->with('error', 'Cannot delete the last admin account.');
            }

            try {
                $admin->delete();
            } catch (\Illuminate\Database\QueryException $e) {
                return back()->with('error',
                    'Cannot delete this user — they have recorded sales or written announcements.'
                );
            }

            return back()->with('success', 'Staff account deleted.');
        }

        // Otherwise it's a customer
        $customer = Customer::findOrFail($id);

        try {
            $customer->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Cannot delete this customer.');
        }

        return back()->with('success', 'Customer deleted.');
    }
}