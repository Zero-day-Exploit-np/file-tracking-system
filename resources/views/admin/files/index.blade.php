@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Department Files</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search file number or name">
        <button>Search</button>
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
                <a href="{{ route('admin.files.show', $file->id) }}">
                    View Timeline
                </a>
            </td>
        </tr>
        @endforeach

    </table>

</div>

@endsection