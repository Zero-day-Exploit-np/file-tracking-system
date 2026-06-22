@extends('layouts.app')
@section('title', 'Public Submissions')

@section('breadcrumb')
<li class="breadcrumb-item active">Public Submissions</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Public File Submissions</h1>
        <div class="page-subtitle">Files submitted by the public for review</div>
    </div>
</div>

<div class="portal-table-wrap">
    <div class="table-responsive">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Subject</th>
                    <th>Attachment</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
            @forelse($files as $file)
            <tr>
                <td class="text-muted">{{ $loop->iteration }}</td>
                <td class="fw-700">{{ $file->applicant_name }}</td>
                <td class="text-muted">{{ $file->email }}</td>
                <td class="text-muted">{{ $file->contact_number }}</td>
                <td>{{ $file->subject }}</td>
                <td>
                    @if($file->attachment_exists)
                    <div class="d-flex gap-1">
                        <a href="{{ $file->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i> View</a>
                        <a href="{{ route('admin.public-files.download', $file->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download"></i></a>
                    </div>
                    @elseif($file->attachment_path)
                    <span class="badge-status badge-pending">File Missing</span>
                    @else
                    <span class="text-muted fs-sm">No attachment</span>
                    @endif
                </td>
                <td class="text-muted fs-sm">{{ $file->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-inbox"></i>No public submissions found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
