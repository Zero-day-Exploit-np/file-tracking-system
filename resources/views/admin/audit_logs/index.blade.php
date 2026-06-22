@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Audit Logs</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="file_number" class="form-control" placeholder="File Number" value="{{ request('file_number') }}">
        </div>
        <div class="col-md-4">
            <select name="action" class="form-control">
                <option value="">All Actions</option>
                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                <option value="requested" {{ request('action') == 'requested' ? 'selected' : '' }}>Requested</option>
                <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('action') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="transferred" {{ request('action') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                <option value="delivered" {{ request('action') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>File</th>
                <th>Action</th>
                <th>From</th>
                <th>To</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                <td>{{ $log->file->file_number ?? 'N/A' }} - {{ $log->file->file_name ?? 'N/A' }}</td>
                <td>{{ ucfirst($log->action) }}</td>
                <td>{{ $log->fromUser->name ?? 'System' }} ({{ $log->fromDept->name ?? 'N/A' }})</td>
                <td>{{ $log->toUser->name ?? '-' }} ({{ $log->toDept->name ?? 'N/A' }})</td>
                <td>{{ $log->remarks }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No audit logs found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $logs->withQueryString()->links() }}
</div>

@endsection