@extends('layouts.app')

@section('content')

<div class="container">
    <div>
        <h4>Pending Transfers</h4>
        <h2>{{ $pendingTransfers }}</h2>
    </div>
    {{-- =======================
        HEADER / ADMIN PROFILE
    ======================== --}}
    <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; background:#f1f5f9; border-radius:12px; margin-bottom:20px;">

        <div>
            <h2 style="margin:0;">Admin Dashboard</h2>
            <p style="margin:5px 0 0 0; color:#555;">
                Welcome back, {{ auth()->user()->name }}
            </p>
        </div>

        <div style="text-align:right;">
            <p style="margin:0;"><strong>{{ auth()->user()->role }}</strong></p>
            <p style="margin:0; font-size:14px; color:#666;">
                {{ auth()->user()->designation->name ?? 'No Designation' }}
            </p>
        </div>

    </div>


    {{-- =======================
        STATS GRID (DASHBOARD KPIs)
    ======================== --}}
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:15px;">

        <div style="padding:20px; background:white; border-radius:12px;">
            <h4>Total Users</h4>
            <h2>{{ $users }}</h2>
        </div>

        <div style="padding:20px; background:white; border-radius:12px;">
            <h4>Departments</h4>
            <h2>{{ $departments }}</h2>
        </div>

        <div style="padding:20px; background:white; border-radius:12px;">
            <h4>Designations</h4>
            <h2>{{ $designations }}</h2>
        </div>

        <div style="padding:20px; background:white; border-radius:12px;">
            <h4>Total Files</h4>
            <h2>{{ $files }}</h2>
        </div>

    </div>

    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px; margin-top:20px;">
        <div style="padding:20px; background:white; border-radius:12px;">
            <h5>Pending Transfers</h5>
            <p class="display-6">{{ $pendingTransfers }}</p>
        </div>

        <div style="padding:20px; background:white; border-radius:12px;">
            <h5>Public Submissions</h5>
            <p class="display-6">{{ $publicSubmissions }}</p>
        </div>

        <div style="padding:20px; background:white; border-radius:12px;">
            <h5>Recent Audit Events</h5>
            <p class="mb-1">{{ $recentAudit->count() }} latest entries</p>
            <small>Updated in real time</small>
        </div>
    </div>

    <div style="margin-top:30px; display:grid; grid-template-columns: 2fr 1fr; gap:20px;">

        <div style="background:white; padding:15px; border-radius:12px;">
            <h3>Recent File Transfers</h3>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransfers as $t)
                    <tr>
                        <td>{{ $t->file->file_name ?? 'N/A' }}</td>
                        <td>{{ $t->sender->name ?? 'System' }}</td>
                        <td>{{ $t->receiver->name ?? 'N/A' }}</td>
                        <td>{{ $t->remarks ?? 'No remarks' }}</td>
                        <td>{{ $t->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No transfers available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="display:grid; gap:20px;">
            <div style="background:white; padding:15px; border-radius:12px;">
                <h3>Recent Users</h3>

                @forelse($recentUsers as $user)
                <div style="padding:10px; border-bottom:1px solid #eee;">
                    <strong>{{ $user->name }}</strong><br>
                    <small>{{ $user->email }}</small><br>
                    <small>{{ $user->department->name ?? 'No Dept' }} | {{ $user->designation->name ?? 'No Designation' }}</small>
                </div>
                @empty
                <p>No recent users found.</p>
                @endforelse
            </div>

            <div style="background:white; padding:15px; border-radius:12px;">
                <h4>Recent Audit Events</h4>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Action</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAudit as $item)
                        <tr>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td>{{ ucfirst($item->action) }}</td>
                            <td>{{ $item->file->file_number ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">No audit activity found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection