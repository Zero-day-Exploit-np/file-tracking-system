@extends('layouts.app')

@section('content')

<div class="container">

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


    {{-- =======================
        MAIN CONTENT GRID
    ======================== --}}
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; margin-top:30px;">


        {{-- LEFT SIDE: TRANSFERS --}}
        <div style="background:white; padding:15px; border-radius:12px;">

            <h3>Recent File Transfers</h3>

            <table width="100%" cellpadding="8" border="0">

                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #ddd;">
                        <th>File</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($recentTransfers as $t)
                    <tr>
                        <td>{{ $t->file->title ?? 'N/A' }}</td>
                        <td>
                            {{ $t->sender->name }}
                            <br>
                            <small>{{ $t->sender->designation->name ?? '' }}</small>
                        </td>
                        <td>
                            {{ $t->receiver->name }}
                            <br>
                            <small>{{ $t->receiver->designation->name ?? '' }}</small>
                        </td>
                        <td>{{ $t->remarks }}</td>
                        <td>{{ $t->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>


        {{-- RIGHT SIDE: USERS ACTIVITY --}}
        <div style="background:white; padding:15px; border-radius:12px;">

            <h3>Recent Users</h3>

            @foreach($recentUsers as $user)
            <div style="padding:10px; border-bottom:1px solid #eee;">

                <strong>{{ $user->name }}</strong>
                <br>

                <small>
                    {{ $user->email }}
                </small>

                <br>

                <small>
                    {{ $user->department->name ?? 'No Dept' }}
                    |
                    {{ $user->designation->name ?? 'No Designation' }}
                </small>

            </div>
            @endforeach

        </div>

    </div>

</div>

@endsection