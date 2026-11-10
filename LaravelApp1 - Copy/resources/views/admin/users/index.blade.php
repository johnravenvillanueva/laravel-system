@extends('layouts.admin')
@section('title','User Management')
@section('page-title','User Management')
@section('content')
  <div class="flex between mb-16">
    <form method="GET" class="filter-bar" style="margin:0">
      <div class="search-box"><input type="text" name="q" class="form-control" placeholder="Search users…" value="{{ request('q') }}"></div>
      <select name="role" class="form-control" style="max-width:160px" onchange="this.form.submit()">
        <option value="">All Roles</option>
        <option value="admin" @selected(request('role')=='admin')>Admin</option>
        <option value="staff" @selected(request('role')=='staff')>Staff</option>
        <option value="customer" @selected(request('role')=='customer')>Customer</option>
      </select>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Ordering activity</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($users ?? [] as $u)
          <tr>
            <td><div class="bold">{{ $u->first_name }} {{ $u->last_name }}</div><div class="text-sm text-muted">{{ $u->phone }}</div></td>
            <td>{{ $u->email }}</td>
            <td><span class="badge badge-confirmed">{{ ucfirst($u->role) }}</span></td>
            <td>@if($u->is_active)<span class="badge badge-ok">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif</td>
            <td>
              @if($u->role === 'customer')
                @if($u->last_order_date)
                  <div class="bold">Last order {{ \Carbon\Carbon::parse($u->last_order_date)->format('M d, Y') }}</div>
                  <div class="text-sm text-muted">{{ $u->inactive_days }} {{ \Illuminate\Support\Str::plural('day', $u->inactive_days) }} since last order</div>
                @else
                  <div class="bold">No orders yet</div>
                  <div class="text-sm text-muted">Account age: {{ $u->inactive_days }} {{ \Illuminate\Support\Str::plural('day', $u->inactive_days) }}</div>
                @endif
              @else
                <span class="text-sm text-muted">—</span>
              @endif
            </td>
            <td class="flex gap-8">
              <a href="{{ route('admin.users.edit', ['id' => $u->id, 'type' => $u->type]) }}" class="btn btn-outline btn-sm">Edit</a>
              @if($u->role === 'customer')
                <form method="POST" action="{{ route('admin.users.customers.status', $u->id) }}" data-confirm="{{ $u->is_active ? 'Deactivate this customer account?' : 'Activate this customer account?' }}">
                  @csrf @method('PUT')
                  <input type="hidden" name="is_active" value="{{ $u->is_active ? 0 : 1 }}">
                  <button class="btn {{ $u->is_active ? 'btn-danger' : 'btn-primary' }} btn-sm">
                    {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.users.destroy',$u->id) }}" data-confirm="Delete this user?">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection