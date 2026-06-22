@extends('layouts.app')
@section('title', 'Department Files')

@section('breadcrumb')
<li class="breadcrumb-item active">Files</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Department Files</h1>
        <div class="page-subtitle">View and manage all files in your department</div>
    </div>
    <a href="{{ route('files.create') }}" class="btn-portal-primary"><i class="fa-solid fa-plus"></i> New File</a>
</div>

<div class="portal-table-wrap">
    <form method="GET" class="table-toolbar">
        <input type="text" name="search" class="form-control" style="max-width:200px;"
            placeholder="File name or number..." value="{{ request('search') }}">
        @if(auth()->user()->role === 'super_admin')
        <select name="department_id" class="form-select" style="max-width:180px;">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->uuid }}" {{ request('department_id') == $dept->uuid ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        @endif
        <select name="status" class="form-select" style="max-width:160px;">
            <option value="">All Statuses</option>
            <option value="active"           {{ request('status') === 'active'           ? 'selected' : '' }}>Active</option>
            <option value="pending_transfer" {{ request('status') === 'pending_transfer' ? 'selected' : '' }}>Pending Transfer</option>
            <option value="archived"         {{ request('status') === 'archived'         ? 'selected' : '' }}>Archived</option>
        </select>
        <input type="date" name="from_date" class="form-control" style="max-width:140px;" value="{{ request('from_date') }}">
        <input type="date" name="to_date"   class="form-control" style="max-width:140px;" value="{{ request('to_date') }}">
        <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa-solid fa-magnifying-glass me-1"></i>Filter</button>
        <a href="{{ route('admin.files') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>File Number</th>
                    <th>File Name</th>
                    <th>Department</th>
                    <th>Current Holder</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($files as $i => $file)
            <tr>
                <td class="text-muted">{{ $files->firstItem() + $i }}</td>
                <td><span class="fw-700 text-portal-primary">{{ $file->file_number }}</span></td>
                <td>{{ $file->file_name }}</td>
                <td class="text-muted">{{ $file->department->name ?? 'N/A' }}</td>
                <td>{{ $file->currentHolder->name ?? 'N/A' }}</td>
                <td>@include('partials.status-badge', ['status' => $file->status])</td>
                <td class="text-muted fs-sm">{{ $file->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.files.timeline', $file->uuid) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-timeline"></i> Timeline
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-file-circle-question"></i>No files found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($files->hasPages())
    <div class="px-4 py-3 border-top">{{ $files->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
