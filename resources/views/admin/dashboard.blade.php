@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

    <div style="display:flex; gap:20px;">

        <div style="padding:20px; background:#f3f3f3;">
            <h3>Total Users</h3>
            <h2>{{ $totalUsers }}</h2>
        </div>

        <div style="padding:20px; background:#f3f3f3;">
            <h3>Total Designations</h3>
            <h2>{{ $totalDesignations }}</h2>
        </div>

    </div>

</div>

@endsection