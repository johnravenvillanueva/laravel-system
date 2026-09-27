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
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($users ?? [] as $u)
          <tr>
            <td><div class="bold">{{ $u->first_name }} {{ $u->last_name }}</div><div class="text-sm text-muted">{{ $u->phone }}</div></td>
            <td>{{ $u->email }}</td>
            <td><span class="badge badge-confirmed">{{ ucfirst($u->role) }}</span></td>
            <td>@if($u->is_active)<span class="badge badge-ok">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif</td>
            <td class="flex gap-8">
              <a href="{{ route('admin.users.edit',$u->id) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.users.destroy',$u->id) }}" data-confirm="Delete this user?">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection