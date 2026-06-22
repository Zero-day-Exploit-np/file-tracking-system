@extends('layouts.app')
@section('title', 'File Timeline')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.files') }}">Files</a></li>
<li class="breadcrumb-item active">Timeline</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $file->file_name }}</h1>
        <div class="page-subtitle">{{ $file->file_number }} &mdash; Movement Timeline</div>
    </div>
    <a href="{{ route('admin.files') }}" class="btn-portal-outline"><i class="fa-solid fa-arrow-left"></i> Back to Files</a>
</div>

{{-- FILE INFO --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-circle-info me-2 text-primary"></i>File Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">File Number</div>
                        <div class="fw-700 text-portal-primary">{{ $file->file_number }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">File Name</div>
                        <div class="fw-700">{{ $file->file_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Current Department</div>
                        <div>{{ $file->department->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Current Holder</div>
                        <div>{{ $file->currentUser->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-sm mb-1">Status</div>
                        @include('partials.status-badge', ['status' => $file->status])
                    </div>
                    @if($file->remarks)
                    <div class="col-12">
                        <div class="text-muted fs-sm mb-1">Remarks</div>
                        <div>{{ $file->remarks }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Movement Summary</div>
            <div class="card-body">
                @php
                    $movements = isset($timeline) ? $timeline : $file->movements;
                    $actionCounts = $movements->groupBy('action');
                @endphp
                @foreach($actionCounts as $action => $items)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    @include('partials.action-badge', ['action' => $action])
                    <span class="fw-700">{{ $items->count() }}</span>
                </div>
                @endforeach
                @if($actionCounts->isEmpty())
                <div class="text-muted fs-sm">No movements yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- TIMELINE --}}
<div class="portal-card">
    <div class="card-header"><i class="fa-solid fa-timeline me-2 text-primary"></i>Movement History</div>
    <div class="card-body">
        @php $moves = isset($timeline) ? $timeline : $file->movements->sortByDesc('created_at'); @endphp
        @if($moves->isEmpty())
        <div class="empty-state"><i class="fa-solid fa-timeline"></i>No movement history recorded.</div>
        @else
        <div class="timeline-wrapper">
            @foreach($moves as $move)
            <div class="timeline-entry">
                <div class="timeline-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        @include('partials.action-badge', ['action' => $move->action])
                        <small class="text-muted">{{ $move->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <div class="text-muted fs-sm">From User</div>
                            <div class="fw-700">{{ $move->fromUser->name ?? 'System' }}</div>
                            <div class="text-muted fs-sm">{{ $move->fromDept->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-sm">To User</div>
                            <div class="fw-700">{{ $move->toUser->name ?? '—' }}</div>
                            <div class="text-muted fs-sm">{{ $move->toDept->name ?? '—' }}</div>
                        </div>
                        @if($move->remarks)
                        <div class="col-12">
                            <div class="text-muted fs-sm"><i class="fa-solid fa-quote-left me-1"></i>{{ $move->remarks }}</div>
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
