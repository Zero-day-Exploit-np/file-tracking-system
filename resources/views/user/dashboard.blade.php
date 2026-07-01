@extends('layouts.app')
@section('title', 'My Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Dashboard</h1>
        <div class="page-subtitle">
            {{ auth()->user()->department->name ?? 'No Department' }}
            &mdash; {{ auth()->user()->designation->name ?? '' }}
        </div>
    </div>
    @if(auth()->user()->role === 'user' && auth()->user()->can_create_file)
    <a href="{{ route('files.create') }}" class="btn-portal-primary">
        <i class="fa-solid fa-plus me-1"></i>New File
    </a>
    @elseif(auth()->user()->role === 'user')
    <span class="badge-status badge-pending">File creation permission not granted</span>
    @endif
</div>

{{-- KPI ROW --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon green"><i class="fa-solid fa-file-lines"></i></div>
            <div>
                <div class="stat-kpi-label">My Files</div>
                <div class="stat-kpi-value">{{ $totalMyFiles }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon blue"><i class="fa-solid fa-paper-plane"></i></div>
            <div>
                <div class="stat-kpi-label">Sent Files</div>
                <div class="stat-kpi-value">{{ $sentFiles }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon teal"><i class="fa-solid fa-inbox"></i></div>
            <div>
                <div class="stat-kpi-label">Received Files</div>
                <div class="stat-kpi-value">{{ $receivedFiles }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon orange"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div class="stat-kpi-label">Pending Transfers</div>
                <div class="stat-kpi-value">{{ $pendingTransfers }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- My Files --}}
    <div class="col-lg-8">
        <div class="portal-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-lines me-2 text-primary"></i>My Files</span>
                <a href="{{ route('files.index') }}" class="btn btn-sm btn-portal-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>File Number</th>
                                <th>File Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myFiles as $file)
                            <tr>
                                <td class="text-muted fs-sm fw-700">{{ $file->file_number }}</td>
                                <td>{{ $file->file_name }}</td>
                                <td>@include('partials.status-badge', ['status' => $file->status])</td>
                                <td class="text-muted fs-sm">{{ $file->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('files.show', $file->uuid) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="fa-solid fa-eye"></i></a>
                                        @if($file->status !== 'archived')
                                        <a href="{{ route('files.transfer.create', $file->uuid) }}" class="btn btn-sm btn-outline-secondary" title="Transfer"><i class="fa-solid fa-right-left"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state"><i class="fa-solid fa-file-circle-question"></i>No files yet. <a href="{{ route('files.create') }}">Create one</a>.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Right column --}}
    <div class="col-lg-4">

        {{-- Unread Notifications --}}
        <div class="portal-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-bell me-2 text-primary"></i>Notifications</span>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-portal-outline">All</a>
            </div>
            <div class="card-body p-0">
                @forelse($unreadNotifications as $n)
                <div class="d-flex gap-3 px-3 py-2 border-bottom notif-unread">
                    <div class="mt-1 text-primary"><i class="fa-solid fa-circle-dot fs-sm"></i></div>
                    <div>
                        <div style="font-size:.845rem;font-weight:600;">{{ $n->data['message'] ?? 'Notification' }}</div>
                        <div class="text-muted fs-sm">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="empty-state py-4"><i class="fa-solid fa-bell-slash"></i>No new notifications.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-timeline me-2 text-primary"></i>Recent Activity</div>
            <div class="card-body p-0">
                <div class="timeline-wrapper p-3">
                    @forelse($recentActivity as $item)
                    <div class="timeline-entry">
                        <div class="timeline-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                @include('partials.action-badge', ['action' => $item->action])
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-muted fs-sm mt-1">
                                {{ $item->file->file_number ?? 'N/A' }}
                                @if($item->toDept) &rarr; {{ $item->toDept->name }} @endif
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