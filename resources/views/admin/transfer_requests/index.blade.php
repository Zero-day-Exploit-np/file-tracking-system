@extends('layouts.app')

@section('content')

<h2>Transfer Requests</h2>

@if(session('success'))
<p style="color:green">{{ session('success') }}</p>
@endif

<!-- TABS -->
<div style="margin-bottom:15px;">
    <button onclick="showTab('pending')">Pending</button>
    <button onclick="showTab('approved')">Approved</button>
    <button onclick="showTab('rejected')">Rejected</button>
</div>

<!-- ================= PENDING ================= -->
<div id="pending" class="tab">

    <h3>Pending Requests</h3>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>File</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="pending-table-body">
            @foreach($pending as $request)
            <tr id="row-{{ $request->id }}" data-request-id="{{ $request->id }}">
                <td>
                    {{ $request->file->file_number ?? 'N/A' }}
                    <br>
                    <small>{{ $request->file->file_name ?? '' }}</small>
                </td>

                <td id="status-{{ $request->id }}" data-status-cell>
                    Pending
                </td>

                <td data-action-cell>
                    <button onclick="approveRequest({{ $request->id }})"
                        style="background:green;color:white;">
                        Approve
                    </button>

                    <button onclick="rejectRequest({{ $request->id }})"
                        style="background:red;color:white;">
                        Reject
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ================= APPROVED ================= -->
<div id="approved" class="tab" style="display:none;">
    <h3>Approved Requests</h3>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>File</th>
                <th>Flow</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($approved as $request)
            <tr>
                <td>
                    <b>{{ $request->file->file_number ?? 'N/A' }}</b><br>
                    <small>{{ $request->file->file_name ?? '' }}</small>
                </td>

                <td>
                    <b>From:</b> {{ $request->sender->name ?? 'Unknown' }}<br>
                    <b>To:</b> {{ $request->receiver->name ?? 'Unknown' }}
                </td>

                <td>
                    <b>{{ $request->fromDept->name ?? 'N/A' }}</b>
                    →
                    <b>{{ $request->toDept->name ?? 'N/A' }}</b>
                </td>

                <td style="color:green;">
                    {{ ucfirst($request->status) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- ================= REJECTED ================= -->
<div id="rejected" class="tab" style="display:none;">
    <h3>Rejected Requests</h3>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>File</th>
                <th>Flow</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($rejected as $request)
            <tr>
                <td>
                    <b>{{ $request->file->file_number ?? 'N/A' }}</b><br>
                    <small>{{ $request->file->file_name ?? '' }}</small>
                </td>

                <td>
                    <b>From:</b> {{ $request->sender->name ?? 'Unknown' }}<br>
                    <b>To:</b> {{ $request->receiver->name ?? 'Unknown' }}
                </td>

                <td>
                    <b>{{ $request->fromDept->name ?? 'N/A' }}</b>
                    →
                    <b>{{ $request->toDept->name ?? 'N/A' }}</b>
                </td>

                <td style="color:red;">
                    {{ ucfirst($request->status) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ================= AJAX SCRIPT ================= -->
<script>
    function approveRequest(id) {
        fetch(`/admin/transfer-requests/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                console.log(data); // DEBUG
                if (data.success) {
                    alert(data.message);
                    location.reload(); // safest way
                }
            })
            .catch(err => console.log(err));
    }

    function rejectRequest(id) {
        fetch(`/admin/transfer-requests/${id}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(err => console.log(err));
    }
</script>


@endsection