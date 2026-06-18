@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Department Files</h2>
    <form method="GET">

        <input type="text"
            name="search"
            placeholder="File Name or Number"
            value="{{ request('search') }}">

        @if(auth()->user()->role == 'super_admin')
        <select name="department_id">
            <option value="">All Departments</option>

            @foreach($departments as $department)
            <option value="{{ $department->id }}">
                {{ $department->name }}
            </option>
            @endforeach
        </select>
        @endif

        <select name="status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="pending_transfer">Pending</option>
            <option value="archived">Archived</option>
        </select>

        <input type="date" name="from_date">
        <input type="date" name="to_date">

        <button type="submit">Search</button>


        <a href="{{ route('admin.files') }}"
            style="padding:8px 12px; background:#ccc; text-decoration:none;">
            Reset Filters
        </a>
    </form>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>File Number</th>
            <th>Remarks</th>
            <th>Timeline</th>
        </tr>

        @foreach($files as $file)
        <tr>
            <td>{{ $file->id }}</td>
            <td>{{ $file->file_name }}</td>
            <td>{{ $file->file_number }}</td>
            <td>{{ $file->remarks }}</td>

            <!-- Timeline link -->
            <td>
                <a href="{{ route('admin.files.timeline', $file->id) }}">
                    View Timeline
                </a>
            </td>
        </tr>
        @endforeach

    </table>

</div>

@endsection