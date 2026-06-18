@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Department Files</h2>

    <x-nav-link :href="route('files.create')">
        Create File
    </x-nav-link>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>File Number</th>
            <th>Remarks</th>
            <th>Actions</th>
        </tr>

        @foreach($files as $file)
        <tr>
            <td>{{ $file->id }}</td>
            <td>{{ $file->file_name }}</td>
            <td>{{ $file->file_number }}</td>
            <td>{{ $file->remarks }}</td>

            <td>
                <a href="{{ route('files.transfer.create', $file->id) }}">
                    Transfer
                </a>
            </td>

            <td>
                <a href="{{ route('admin.files.timeline', $file->id) }}">
                    Timeline
                </a>
            </td>
        </tr>
        @endforeach

    </table>

</div>

@endsection