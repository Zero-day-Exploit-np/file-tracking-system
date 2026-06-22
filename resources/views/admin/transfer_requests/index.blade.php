@extends('layouts.app')
@section('title', 'Transfer Requests')

@section('breadcrumb')
<li class="breadcrumb-item active">Transfer Requests</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Transfer Requests</h1>
        <div class="page-subtitle">Review and manage incoming file transfer requests</div>
    </div>
</div>

{{-- STATS ROW --}}
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stat-kpi">
            <div class="stat-kpi-icon orange"><i class="fa-solid fa-clock"></i></div>
            <div><div class="stat-kpi-label">Pending</div><div class="stat-kpi-value">{{ $pending->count() }}</div></div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-kpi">
            <div class="stat-kpi-icon green"><i class="fa-solid fa-check"></i></div>
            <div><div class="stat-kpi-label">Approved</div><div class="stat-kpi-value">{{ $approved->count() }}</div></div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-kpi">
            <div class="stat-kpi-icon red"><i class="fa-solid fa-xmark"></i></div>
            <div><div class="stat-kpi-label">Rejected</div><div class="stat-kpi-value">{{ $rejected->count() }}</div></div>
        </div>
    </div>
</div>

{{-- TABS --}}
<ul class="nav nav-tabs mb-3" id="transferTabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pending">
            Pending <span class="badge bg-warning text-dark ms-1">{{ $pending->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-approved">
            Approved <span class="badge bg-success ms-1">{{ $approved->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rejected">
            Rejected <span class="badge bg-danger ms-1">{{ $rejected->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- PENDING --}}
    <div class="tab-pane fade show active" id="tab-pending">
        <div class="portal-table-wrap">
            <div class="table-responsive">
                <table class="portal-table">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Requested By</th>
                            <th>From Dept.</th>
                            <th>To Dept.</th>
                            <th>Target User</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pending as $req)
                    <tr id="row-{{ $req->id }}">
                        <td>
                            <div class="fw-700">{{ $req->file->file_name ?? 'N/A' }}</div>
                            <div class="text-muted fs-sm">{{ $req->file->file_number ?? '' }}</div>
                        </td>
                        <td>{{ $req->sender->name ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $req->fromDept->name ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $req->toDept->name ?? 'N/A' }}</td>
                        <td>{{ $req->receiver->name ?? 'N/A' }}</td>
                        <td class="text-muted fs-sm">{{ $req->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button onclick="handleRequest({{ $req->id }}, 'approve')" class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-check"></i> Approve
                                </button>
                                <button onclick="handleRequest({{ $req->id }}, 'reject')" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-clock"></i>No pending requests.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- APPROVED --}}
    <div class="tab-pane fade" id="tab-approved">
        <div class="portal-table-wrap">
            <div class="table-responsive">
                <table class="portal-table">
                    <thead><tr><th>File</th><th>From</th><th>To</th><th>From Dept.</th><th>To Dept.</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($approved as $req)
                    <tr>
                        <td><div class="fw-700">{{ $req->file->file_name ?? 'N/A' }}</div><div class="text-muted fs-sm">{{ $req->file->file_number ?? '' }}</div></td>
                        <td>{{ $req->sender->name ?? 'N/A' }}</td>
                        <td>{{ $req->receiver->name ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $req->fromDept->name ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $req->toDept->name ?? 'N/A' }}</td>
                        <td class="text-muted fs-sm">{{ $req->updated_at->format('d M Y') }}</td>
                        <td>@include('partials.status-badge', ['status' => 'approved'])</td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-check"></i>No approved requests.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- REJECTED --}}
    <div class="tab-pane fade" id="tab-rejected">
        <div class="portal-table-wrap">
            <div class="table-responsive">
                <table class="portal-table">
                    <thead><tr><th>File</th><th>From</th><th>To</th><th>From Dept.</th><th>To Dept.</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($rejected as $req)
                    <tr>
                        <td><div class="fw-700">{{ $req->file->file_name ?? 'N/A' }}</div><div class="text-muted fs-sm">{{ $req->file->file_number ?? '' }}</div></td>
                        <td>{{ $req->sender->name ?? 'N/A' }}</td>
                        <td>{{ $req->receiver->name ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $req->fromDept->name ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $req->toDept->name ?? 'N/A' }}</td>
                        <td class="text-muted fs-sm">{{ $req->updated_at->format('d M Y') }}</td>
                        <td>@include('partials.status-badge', ['status' => 'rejected'])</td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-xmark"></i>No rejected requests.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="requestFeedback" class="alert d-none mt-3" role="alert"></div>

@push('scripts')
<script>
function handleRequest(id, action) {
    const label = action === 'approve' ? 'Approve' : 'Reject';
    if (!confirm(`${label} this transfer request?`)) return;

    fetch(`/admin/transfer-requests/${id}/${action}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`row-${id}`);
            if (row) row.remove();
            const fb = document.getElementById('requestFeedback');
            fb.className = 'alert alert-success mt-3';
            fb.textContent = data.message;
        }
    })
    .catch(() => {
        alert('An error occurred. Please refresh and try again.');
    });
}
</script>
@endpush
@endsection
