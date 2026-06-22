@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
@php
    $user    = auth()->user();
    $isSuper = $user->role === 'super_admin';
    $col     = $isSuper ? 'col-lg-2' : 'col-lg-3';
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $isSuper ? 'Super Admin' : 'Admin' }} Dashboard</h1>
        <div class="page-subtitle">Welcome back, {{ $user->name }}
            &mdash; {{ $user->designation->name ?? ucfirst(str_replace('_', ' ', $user->role)) }}
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('files.create') }}" class="btn-portal-primary">
            <i class="fa-solid fa-plus"></i> New File
        </a>
    </div>
</div>

{{-- KPI CARDS --}}
<div class="row g-3 mb-4">

    <div class="col-6 {{ $col }}">
        <div class="stat-kpi">
            <div class="stat-kpi-icon blue"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-kpi-label">{{ $isSuper ? 'Total' : 'Dept.' }} Users</div>
                <div class="stat-kpi-value">{{ $totalUsers }}</div>
            </div>
        </div>
    </div>

    @if($isSuper)
    <div class="col-6 col-lg-2">
        <div class="stat-kpi">
            <div class="stat-kpi-icon purple"><i class="fa-solid fa-building-columns"></i></div>
            <div>
                <div class="stat-kpi-label">Departments</div>
                <div class="stat-kpi-value">{{ $totalDepartments }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-6 {{ $col }}">
        <div class="stat-kpi">
            <div class="stat-kpi-icon teal"><i class="fa-solid fa-id-badge"></i></div>
            <div>
                <div class="stat-kpi-label">Designations</div>
                <div class="stat-kpi-value">{{ $totalDesignations }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 {{ $col }}">
        <div class="stat-kpi">
            <div class="stat-kpi-icon green"><i class="fa-solid fa-file-lines"></i></div>
            <div>
                <div class="stat-kpi-label">Total Files</div>
                <div class="stat-kpi-value">{{ $totalFiles }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 {{ $col }}">
        <div class="stat-kpi">
            <div class="stat-kpi-icon orange"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div class="stat-kpi-label">Pending Transfers</div>
                <div class="stat-kpi-value">{{ $pendingTransfers }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 {{ $col }}">
        <div class="stat-kpi">
            <div class="stat-kpi-icon red"><i class="fa-solid fa-cloud-arrow-up"></i></div>
            <div>
                <div class="stat-kpi-label">Public Uploads</div>
                <div class="stat-kpi-value">{{ $publicSubmissions }}</div>
            </div>
        </div>
    </div>

</div>
{{-- END KPI --}}

<div class="row g-3 mb-4">

    {{-- Recent Transfers --}}
    <div class="col-lg-7">
        <div class="portal-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-right-left me-2 text-primary"></i>Recent File Transfers</span>
                <a href="{{ route('admin.transfer.requests') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentTransfers as $t)
                        <tr>
                            <td>
                                <span class="fw-700">{{ $t->file->file_name ?? 'N/A' }}</span><br>
                                <span class="text-muted fs-sm">{{ $t->file->file_number ?? '' }}</span>
                            </td>
                            <td>{{ $t->sender->name ?? 'System' }}</td>
                            <td>{{ $t->receiver->name ?? 'N/A' }}</td>
                            <td class="text-muted fs-sm">{{ $t->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No transfers yet.</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="col-lg-5">
        <div class="portal-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-users me-2 text-primary"></i>Recent Users</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentUsers as $u)
                <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom">
                    <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;color:#2563eb;
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-700 text-truncate" style="font-size:.85rem;">{{ $u->name }}</div>
                        <div class="text-muted fs-sm">
                            {{ $u->department->name ?? 'No Dept' }}
                            &middot;
                            {{ $u->designation->name ?? 'No Designation' }}
                        </div>
                    </div>
                    <span class="badge-status badge-role-{{ $u->role }}">
                        {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                    </span>
                </div>
                @empty
                <div class="empty-state"><i class="fa-solid fa-users"></i>No users found.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<div class="row g-3">

    {{-- Recent Files --}}
    <div class="col-lg-7">
        <div class="portal-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-lines me-2 text-primary"></i>Recent Files</span>
                <a href="{{ route('admin.files') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>File Number</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentFiles as $f)
                        <tr>
                            <td class="text-muted fs-sm">{{ $f->file_number }}</td>
                            <td class="fw-700">{{ $f->file_name }}</td>
                            <td class="text-muted">{{ $f->department->name ?? 'N/A' }}</td>
                            <td>@include('partials.status-badge', ['status' => $f->status])</td>
                            <td>
                                <a href="{{ route('admin.files.timeline', $f->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Timeline
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No files found.</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Audit Activity --}}
    <div class="col-lg-5">
        <div class="portal-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-list-check me-2 text-primary"></i>Recent Activity</span>
                <a href="{{ route('admin.audit.logs') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="timeline-wrapper p-4">
                    @forelse($recentAudit as $item)
                    <div class="timeline-entry">
                        <div class="timeline-card">
                            <div class="d-flex justify-content-between align-items-start">
                                @include('partials.action-badge', ['action' => $item->action])
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="mt-1 text-muted fs-sm">
                                {{ $item->file->file_number ?? 'N/A' }}
                                &mdash;
                                {{ $item->fromUser->name ?? 'System' }}
                                &rarr;
                                {{ $item->toUser->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state"><i class="fa-solid fa-inbox"></i>No activity yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
