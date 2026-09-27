@extends('layouts.admin')
@section('title','Announcements')
@section('page-title','Announcements')
@section('content')
  <div class="flex between mb-16">
    <h3 class="text-lg bold">All Announcements</h3>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">+ New Announcement</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Title</th><th>Target</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($announcements ?? [] as $a)
          <tr>
            <td class="bold">{{ $a->title }}</td>
            <td>{{ ucfirst($a->target_audience) }}</td>
            <td>@if($a->is_active)<span class="badge badge-ok">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif</td>
            <td class="text-sm text-muted">{{ $a->created_at->format('M d, Y') }}</td>
            <td class="flex gap-8">
              <a href="{{ route('admin.announcements.edit',$a->id) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.announcements.destroy',$a->id) }}" data-confirm="Delete this announcement?">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">No announcements yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection