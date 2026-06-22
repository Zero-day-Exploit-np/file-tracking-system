@extends('layouts.app')
@section('title', 'Audit Logs')

@section('breadcrumb')
<li class="breadcrumb-item active">Audit Logs</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Audit Logs</h1>
        <div class="page-subtitle">Complete file movement and activity history</div>
    </div>
</div>

<div class="portal-table-wrap">
    <form method="GET" class="table-toolbar">
        <input type="text" name="file_number" class="form-control" style="max-width:180px;"
            placeholder="File number..." value="{{ request('file_number') }}">
        <select name="action" class="form-select" style="max-width:160px;">
            <option value="">All Actions</option>
            <option value="created"     {{ request('action') === 'created'     ? 'selected' : '' }}>Created</option>
            <option value="requested"   {{ request('action') === 'requested'   ? 'selected' : '' }}>Requested</option>
            <option value="approved"    {{ request('action') === 'approved'    ? 'selected' : '' }}>Approved</option>
            <option value="rejected"    {{ request('action') === 'rejected'    ? 'selected' : '' }}>Rejected</option>
            <option value="transferred" {{ request('action') === 'transferred' ? 'selected' : '' }}>Transferred</option>
            <option value="delivered"   {{ request('action') === 'delivered'   ? 'selected' : '' }}>Delivered</option>
        </select>
        <input type="date" name="from_date" class="form-control" style="max-width:140px;" value="{{ request('from_date') }}">
        <input type="date" name="to_date"   class="form-control" style="max-width:140px;" value="{{ request('to_date') }}">
        <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa-solid fa-magnifying-glass me-1"></i>Filter</button>
        <a href="{{ route('admin.audit.logs') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>File</th>
                    <th>Action</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="text-muted fs-sm">{{ $log->created_at->format('d M Y') }}<br>{{ $log->created_at->format('h:i A') }}</td>
                <td>
                    <div class="fw-700">{{ $log->file->file_name ?? 'N/A' }}</div>
                    <div class="text-muted fs-sm">{{ $log->file->file_number ?? '' }}</div>
                </td>
                <td>@include('partials.action-badge', ['action' => $log->action])</td>
                <td>
                    <div>{{ $log->fromUser->name ?? 'System' }}</div>
                    <div class="text-muted fs-sm">{{ $log->fromDept->name ?? '—' }}</div>
                </td>
                <td>
                    <div>{{ $log->toUser->name ?? '—' }}</div>
                    <div class="text-muted fs-sm">{{ $log->toDept->name ?? '—' }}</div>
                </td>
                <td class="text-muted fs-sm">{{ $log->remarks ?: '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-list-check"></i>No audit logs found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
