@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Admin Dashboard</h2>

    <div style="display:flex; gap:20px; margin-top:20px;">

        <div style="padding:20px; background:#eee;">
            <h3>Users</h3>
            <p>{{ $users }}</p>
        </div>

        <div style="padding:20px; background:#eee;">
            <h3>Departments</h3>
            <p>{{ $departments }}</p>
        </div>

        <div style="padding:20px; background:#eee;">
            <h3>Designations</h3>
            <p>{{ $designations }}</p>
        </div>

        <div style="padding:20px; background:#eee;">
            <h3>Files</h3>
            <p>{{ $files }}</p>
        </div>

    </div>

</div>

@endsection