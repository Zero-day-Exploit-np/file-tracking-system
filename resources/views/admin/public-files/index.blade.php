@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Public Uploaded Files</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table">

        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>File</th>
            <th>Date</th>
        </tr>

        @forelse($files as $file)
        <tr>
            <td>{{ $file->applicant_name }}</td>
            <td>{{ $file->email }}</td>
            <td>{{ $file->subject }}</td>
            <td>
                @if($file->attachment_exists)
                <a href="{{ $file->attachment_url }}" target="_blank" class="btn btn-sm btn-primary me-2">View</a>
                <a href="{{ route('admin.public-files.download', $file->id) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                @elseif($file->attachment_path)
                <span class="text-warning">File missing</span>
                @else
                <span class="text-muted">No attachment</span>
                @endif
            </td>
            <td>{{ $file->created_at->format('d-m-Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No public submissions found.</td>
        </tr>
        @endforelse

    </table>

</div>

@endsection