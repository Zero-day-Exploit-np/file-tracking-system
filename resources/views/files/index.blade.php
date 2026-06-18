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
            <th>Status</th>
            <th>Transfer</th>


            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin')

            <th>Timeline</th>
            @endif
        </tr>

        @foreach($files as $file)
        <tr>

            <td>{{ $file->id }}</td>

            <td>{{ $file->file_name }}</td>

            <td>{{ $file->file_number }}</td>

            <td>{{ $file->remarks }}</td>

            <td>
                @if($file->status == 'active')
                Active
                @elseif($file->status == 'pending_transfer')
                Pending Approval
                @elseif($file->status == 'archived')
                Archived
                @else
                Draft
                @endif
            </td>

            <td>
                <a href="{{ route('files.transfer.create', $file->id) }}">
                    Transfer
                </a>
            </td>

            <td>

                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin')
                <a href="{{ route('admin.files.timeline', $file->id) }}">
                    Timeline
                </a>
                @endif

            </td>

        </tr>
        @endforeach

    </table>

</div>

@endsection