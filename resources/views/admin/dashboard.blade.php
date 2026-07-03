@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Admin Dashboard</li>
@endsection

@section('content')
@php $deptName = auth()->user()->department->name ?? 'Your Department'; @endphp

<div class="page-header">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <div class="page-subtitle">{{ $deptName }} &mdash; Welcome, {{ auth()->user()->name }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.files') }}" class="btn-portal-outline">
            <i class="fa-solid fa-folder-open me-1"></i>All Files
        </a>
        <a href="{{ route('public.file.search') }}" class="btn-portal-outline">
            <i class="fa-solid fa-magnifying-glass me-1"></i>File Search
        </a>
    </div>
</div>

{{-- KPI ROW --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-kpi">
            <div class="stat-kpi-icon green"><i class="fa-solid fa-file-lines"></i></div>
            <div>
                <div class="stat-kpi-label">Dept. Files</div>
                <div class="stat-kpi-value">{{ $deptFiles }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-kpi">
            <div class="stat-kpi-icon blue"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-kpi-label">Users in Dept.</div>
                <div class="stat-kpi-value">{{ $deptUsers }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-kpi">
            <div class="stat-kpi-icon teal"><i class="fa-solid fa-right-left"></i></div>
            <div>
                <div class="stat-kpi-label">Total Transfers</div>
                <div class="stat-kpi-value">{{ $totalTransfers }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    {{-- Recent Files --}}
    <div class="col-lg-7">
        <div class="portal-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-lines me-2 text-primary"></i>Department Files</span>
                <a href="{{ route('admin.files') }}" class="btn btn-sm btn-portal-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>File Number</th>
                                <th>Name</th>
                                <th>Current Holder</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFiles as $f)
                            <tr>
                                <td class="text-muted fs-sm">{{ $f->file_number }}</td>
                                <td class="fw-700">{{ $f->file_name }}</td>
                                <td class="text-muted">{{ $f->currentHolder->name ?? 'N/A' }}</td>
                                <td>@include('partials.status-badge', ['status' => $f->status])</td>
                                <td>
                                    <a href="{{ route('admin.files.timeline', $f->uuid) }}"
                                       class="btn btn-sm btn-portal-outline" title="Timeline">
                                        <i class="fa-solid fa-timeline"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">No files found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Users in Department --}}
    <div class="col-lg-5">
        <div class="portal-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-users me-2 text-primary"></i>Users in {{ $deptName }}</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-portal-outline">Manage</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentUsers as $u)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;color:#2563eb;
                                display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-700 text-truncate" style="font-size:.845rem;">{{ $u->name }}</div>
                        <div class="text-muted fs-sm">{{ $u->designation->name ?? 'No Designation' }}</div>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fa-solid fa-users"></i>No users found.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Recent Activity --}}
<div class="portal-card">
    <div class="card-header">
        <span><i class="fa-solid fa-list-check me-2 text-primary"></i>Recent File Movements</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Action</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Department</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity as $item)
                    <tr>
                        <td>
                            <div class="fw-700">{{ $item->file->file_name ?? 'N/A' }}</div>
                            <div class="text-muted fs-sm">{{ $item->file->file_number ?? '' }}</div>
                        </td>
                        <td>@include('partials.action-badge', ['action' => $item->action])</td>
                        <td class="text-muted">{{ $item->fromUser->name ?? 'System' }}</td>
                        <td class="text-muted">{{ $item->toUser->name ?? '—' }}</td>
                        <td class="text-muted fs-sm">{{ $item->toDept->name ?? '—' }}</td>
                        <td class="text-muted fs-sm">{{ $item->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-inbox"></i>No activity yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
