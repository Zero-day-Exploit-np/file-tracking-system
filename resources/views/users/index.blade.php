@extends('layouts.app')
@section('title', 'Admin Users')

@section('breadcrumb')
<li class="breadcrumb-item active">Admin Users</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Admin Users</h1>
        <div class="page-subtitle">Manage all administrators and super admins</div>
    </div>
    <a href="{{ route('users.create') }}" class="btn-portal-primary"><i class="fa-solid fa-plus"></i> Create Admin</a>
</div>

<div class="portal-table-wrap">
    <div class="table-responsive">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td class="text-muted">{{ $loop->iteration }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                        <div class="fw-700">{{ $user->name }}</div>
                    </div>
                </td>
                <td class="text-muted">{{ $user->email }}</td>
                <td><span class="badge-status badge-role-{{ $user->role }}">{{ ucfirst(str_replace('_',' ',$user->role)) }}</span></td>
                <td class="text-muted">{{ $user->department->name ?? '—' }}</td>
                <td class="text-muted">{{ $user->designation->name ?? '—' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('users.edit', $user->uuid) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user->uuid) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Delete this user?')"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-users"></i>No admin users found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-3 border-top">{{ $users->links() }}</div>
    @endif
</div>
@endsection
