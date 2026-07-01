@extends('layouts.app')
@section('title', 'File Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">{{ $file->file_number }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $file->file_name }}</h1>
        <div class="page-subtitle">{{ $file->file_number }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('files.index') }}" class="btn-portal-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        @can('update', $file)
        <a href="{{ route('files.edit', $file->uuid) }}" class="btn btn-secondary"><i class="fa-solid fa-pencil"></i> Edit</a>
        @endcan
        @if($file->attachment_path)
        <a href="{{ route('files.download', $file->uuid) }}" class="btn btn-success"><i class="fa-solid fa-download"></i> Download Document</a>
        @endif
        @if($file->status !== 'archived')
        <a href="{{ route('files.transfer.create', $file->uuid) }}" class="btn-portal-primary"><i class="fa-solid fa-right-left"></i> Transfer</a>
        @endif
    </div>
</div>

{{-- FILE INFO CARD --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-circle-info me-2 text-primary"></i>File Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">File Name</div>
                        <div class="fw-700">{{ $file->file_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">File Number</div>
                        <div class="fw-700 text-portal-primary">{{ $file->file_number }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Department</div>
                        <div>{{ $file->department->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Status</div>
                        <div>@include('partials.status-badge', ['status' => $file->status])</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Created By</div>
                        <div>{{ $file->creator->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Current Holder</div>
                        <div>{{ $file->currentHolder->name ?? 'N/A' }}</div>
                    </div>
                    @if($file->remarks)
                    <div class="col-12">
                        <div class="text-muted fs-sm mb-1">Remarks</div>
                        <div>{{ $file->remarks }}</div>
                    </div>
                    @endif
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Created At</div>
                        <div>{{ $file->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    @if($file->attachment_name)
                    <div class="col-12">
                        <div class="text-muted fs-sm mb-1">Attached Document</div>
                        <div>{{ $file->attachment_name }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Quick Stats</div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-sm">Total Movements</span>
                        <span class="fw-700">{{ $file->movements->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-sm">Last Activity</span>
                        <span class="fw-700">{{ $file->movements->last()?->created_at?->diffForHumans() ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MOVEMENT TIMELINE --}}
<div class="portal-card">
    <div class="card-header"><i class="fa-solid fa-timeline me-2 text-primary"></i>Movement History</div>
    <div class="card-body">
        @if($file->movements->isEmpty())
        <div class="empty-state"><i class="fa-solid fa-timeline"></i>No movement history available.</div>
        @else
        <div class="timeline-wrapper">
            @foreach($file->movements->sortByDesc('created_at') as $move)
            <div class="timeline-entry">
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        @include('partials.action-badge', ['action' => $move->action])
                        <small class="text-muted">{{ $move->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-sm-6">
                            <div class="text-muted fs-sm">From</div>
                            <div class="fw-700">{{ $move->fromUser->name ?? 'System' }}</div>
                            <div class="text-muted fs-sm">{{ $move->fromDept->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-sm">To</div>
                            <div class="fw-700">{{ $move->toUser->name ?? '—' }}</div>
                            <div class="text-muted fs-sm">{{ $move->toDept->name ?? '—' }}</div>
                        </div>
                        @if($move->remarks)
                        <div class="col-12">
                            <div class="text-muted fs-sm">Remarks: {{ $move->remarks }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
