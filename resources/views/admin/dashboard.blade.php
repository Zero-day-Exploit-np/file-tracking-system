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
        <a href="{{ route('admin.transfer.requests') }}" class="btn-portal-outline">
            <i class="fa-solid fa-right-left me-1"></i>Transfer Requests
            @if($pendingRequests > 0)<span class="badge bg-warning text-dark ms-1">{{ $pendingRequests }}</span>@endif
        </a>
        <a href="{{ route('public.file.search') }}" class="btn-portal-outline">
            <i class="fa-solid fa-magnifying-glass me-1"></i>File Search
        </a>
    </div>
</div>

{{-- KPI ROW --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon green"><i class="fa-solid fa-file-lines"></i></div>
            <div>
                <div class="stat-kpi-label">Dept. Files</div>
                <div class="stat-kpi-value">{{ $deptFiles }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon orange"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div class="stat-kpi-label">Pending Requests</div>
                <div class="stat-kpi-value">{{ $pendingRequests }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon blue"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-kpi-label">Users in Dept.</div>
                <div class="stat-kpi-value">{{ $deptUsers }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-kpi">
            <div class="stat-kpi-icon teal"><i class="fa-solid fa-check-double"></i></div>
            <div>
                <div class="stat-kpi-label">Completed Transfers</div>
                <div class="stat-kpi-value">{{ $completedTransfers }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    {{-- Pending Approvals — action panel --}}
    <div class="col-lg-7">
        <div class="portal-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fa-solid fa-right-left me-2 text-primary"></i>Pending Approvals for {{ $deptName }}
                    <span id="pending-badge" class="badge bg-warning text-dark ms-1"
                          style="{{ $pendingApprovals->count() > 0 ? '' : 'display:none;' }}">
                        {{ $pendingApprovals->count() }}
                    </span>
                </span>
                <a href="{{ route('admin.transfer.requests') }}" class="btn btn-sm btn-portal-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>From</th>
                                <th>Requested By</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="pending-approvals-tbody">
                            @forelse($pendingApprovals as $req)
                            <tr id="dash-row-{{ $req->uuid }}">
                                <td>
                                    <div class="fw-700">{{ $req->file->file_name ?? 'N/A' }}</div>
                                    <div class="text-muted fs-sm">{{ $req->file->file_number ?? '' }}</div>
                                </td>
                                <td class="text-muted">{{ $req->fromDept->name ?? 'N/A' }}</td>
                                <td>{{ $req->sender->name ?? 'N/A' }}</td>
                                <td class="text-muted fs-sm">{{ $req->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button onclick="dashAction('{{ $req->uuid }}', 'approve', this)"
                                            class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button onclick="dashAction('{{ $req->uuid }}', 'reject', this)"
                                            class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state py-3"><i class="fa-solid fa-check"></i>No pending approvals.</div>
                                </td>
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

<div class="row g-3">
    {{-- Recent Files --}}
    <div class="col-lg-7">
        <div class="portal-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-file-lines me-2 text-primary"></i>Recent Department Files</span>
                <a href="{{ route('admin.files') }}" class="btn btn-sm btn-portal-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>File Number</th>
                                <th>Name</th>
                                <th>Holder</th>
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
                                <td><a href="{{ route('admin.files.timeline', $f->uuid) }}" class="btn btn-sm btn-portal-outline">Timeline</a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No files found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-5">
        <div class="portal-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-list-check me-2 text-primary"></i>Recent Activity</span>
                <a href="{{ route('admin.audit.logs') }}" class="btn btn-sm btn-portal-outline">Full Log</a>
            </div>
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
                                @if($item->fromUser) &mdash; {{ $item->fromUser->name }} @endif
                                @if($item->toUser) &rarr; {{ $item->toUser->name }} @endif
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

<div id="dashFeedback" class="alert d-none mt-3"></div>

@push('scripts')
<script>
// ── Approve / Reject action ───────────────────────────────────────
function dashAction(uuid, action, btn) {
    var label = action === 'approve' ? 'Approve' : 'Reject';
    if (!confirm(label + ' this transfer request?')) return;
    var originalHtml = btn ? btn.innerHTML : null;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }

    fetch('/admin/transfer-requests/' + uuid + '/' + action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(function(r) { return r.json().then(function(d){ return { ok: r.ok, data: d }; }); })
    .then(function(res) {
        var fb = document.getElementById('dashFeedback');
        if (res.data.success) {
            // Remove row immediately, then refresh via poll
            var row = document.getElementById('dash-row-' + uuid);
            if (row) row.remove();
            fb.className = 'alert alert-success mt-3';
            fb.textContent = res.data.message;
            // Force an immediate poll to refresh counts and rows
            pollDashboard();
        } else {
            fb.className = 'alert alert-danger mt-3';
            fb.textContent = res.data.message || 'Action failed.';
            if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
        }
        setTimeout(function() { fb.className = 'alert d-none'; }, 5000);
    })
    .catch(function() {
        alert('Network error. Please refresh.');
        if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
    });
}

// ── Dashboard live polling every 10 seconds ───────────────────────
var lastPendingCount = {{ $pendingApprovals->count() }};

function pollDashboard() {
    fetch('{{ route("admin.dashboard.poll") }}', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
        },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.ok ? r.json() : null; })
    .then(function(data) {
        if (!data) return;

        var count   = data.pending_count || 0;
        var badge   = document.getElementById('pending-badge');
        var tbody   = document.getElementById('pending-approvals-tbody');
        var kpiVal  = document.querySelector('.stat-kpi-icon.orange + div .stat-kpi-value');

        // Update badge in card header
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
            }
        }

        // Update KPI card value
        if (kpiVal) kpiVal.textContent = count;

        // Play sound if new requests arrived
        if (count > lastPendingCount) {
            var sound = document.getElementById('notif-sound');
            if (sound) { sound.currentTime = 0; sound.play().catch(function(){}); }
        }
        lastPendingCount = count;

        // Refresh table rows (only if count changed to avoid flickering)
        if (tbody && data.rows !== undefined) {
            tbody.innerHTML = data.rows;
        }
    })
    .catch(function() {});
}

// Start polling after 10s, then every 10s
setTimeout(function() { pollDashboard(); setInterval(pollDashboard, 10000); }, 10000);
</script>
@endpush
@endsection