@extends('layouts.app')
@section('title', 'File Timeline')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.files') }}">Files</a></li>
<li class="breadcrumb-item active">Timeline</li>
@endsection

@push('styles')
<style>
/* ── Horizontal Timeline ──────────────────────────────────── */
.htl-scroll-wrap {
    overflow-x: auto;
    padding-bottom: .5rem;
}
.htl {
    display: flex;
    align-items: flex-start;
    gap: 0;
    min-width: max-content;
    padding: 1.5rem 1rem;
}
.htl-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
}
.htl-step + .htl-step::before {
    content: '';
    position: absolute;
    left: -40px;
    top: 22px;
    width: 40px;
    height: 2px;
    background: #6366f1;
}
.htl-node {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    background: #6366f1;
    box-shadow: 0 0 0 4px #ede9fe;
    position: relative;
    z-index: 1;
}
.htl-node.node-created    { background: #7c3aed; box-shadow: 0 0 0 4px #ede9fe; }
.htl-node.node-transferred { background: #2563eb; box-shadow: 0 0 0 4px #dbeafe; }
.htl-node.node-dept       { background: #059669; box-shadow: 0 0 0 4px #d1fae5; }

.htl-connector {
    width: 60px;
    height: 2px;
    background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
    position: relative;
    align-self: center;
    flex-shrink: 0;
}
.htl-connector::after {
    content: '';
    position: absolute;
    right: -6px;
    top: -5px;
    border: 6px solid transparent;
    border-left-color: #818cf8;
}

.htl-label {
    margin-top: .65rem;
    max-width: 120px;
}
.htl-label .name {
    font-size: .82rem;
    font-weight: 700;
    color: #1e293b;
    word-break: break-word;
}
.htl-label .sub {
    font-size: .72rem;
    color: #64748b;
    margin-top: 2px;
}
.htl-label .date {
    font-size: .70rem;
    color: #94a3b8;
    margin-top: 2px;
}
.htl-label .remarks-tag {
    display: inline-block;
    background: #f1f5f9;
    border-radius: 4px;
    padding: 1px 6px;
    font-size: .68rem;
    color: #64748b;
    margin-top: 3px;
    word-break: break-word;
    max-width: 120px;
}

/* vertical fallback for tiny screens */
@media (max-width: 576px) {
    .htl { flex-direction: column; align-items: flex-start; min-width: unset; }
    .htl-connector { width: 2px; height: 40px; background: linear-gradient(180deg, #6366f1 0%, #818cf8 100%); align-self: flex-start; margin-left: 21px; }
    .htl-connector::after {
        right: unset; top: unset;
        bottom: -6px; left: -5px;
        border-left-color: transparent;
        border-top-color: #818cf8;
    }
    .htl-step { flex-direction: row; align-items: flex-start; gap: 1rem; text-align: left; }
    .htl-step::before { display: none; }
    .htl-label { margin-top: 0; max-width: 200px; }
}
</style>
@endpush

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

{{-- HORIZONTAL TIMELINE --}}
<div class="portal-card">
    <div class="card-header"><i class="fa-solid fa-timeline me-2 text-primary"></i>File Journey</div>
    <div class="card-body">
        @php $moves = (isset($timeline) ? $timeline : $file->movements)->sortBy('created_at'); @endphp
        @if($moves->isEmpty())
        <div class="empty-state"><i class="fa-solid fa-timeline"></i>No movement history recorded.</div>
        @else

        {{-- Horizontal scrollable timeline --}}
        <div class="htl-scroll-wrap">
            <div class="htl">
                @foreach($moves as $i => $move)

                @php
                    $isDeptMove = $move->fromDept && $move->toDept &&
                                  $move->fromDept->id !== $move->toDept->id;
                    $nodeClass = $move->action === 'created' ? 'node-created' : ($isDeptMove ? 'node-dept' : 'node-transferred');
                    $icon = match($move->action) {
                        'created'     => 'fa-file-circle-plus',
                        'transferred' => 'fa-right-left',
                        default       => 'fa-circle-dot',
                    };
                @endphp

                @if($i > 0)
                <div class="htl-connector"></div>
                @endif

                <div class="htl-step">
                    <div class="htl-node {{ $nodeClass }}">
                        <i class="fa-solid {{ $icon }} fa-sm"></i>
                    </div>
                    <div class="htl-label">
                        <div class="name">
                            @if($move->action === 'created')
                                {{ $move->fromUser->name ?? 'System' }}
                            @else
                                {{ $move->toUser->name ?? $move->toDept->name ?? '—' }}
                            @endif
                        </div>
                        <div class="sub">
                            @if($move->action === 'created')
                                {{ $move->fromDept->name ?? '—' }}
                            @else
                                {{ $move->toDept->name ?? '—' }}
                            @endif
                        </div>
                        <div class="date">{{ $move->created_at->format('d M Y') }}<br>{{ $move->created_at->format('h:i A') }}</div>
                        @if($move->remarks)
                        <div class="remarks-tag" title="{{ $move->remarks }}">
                            <i class="fa-solid fa-quote-left fa-xs me-1"></i>{{ Str::limit($move->remarks, 30) }}
                        </div>
                        @endif
                    </div>
                </div>

                @endforeach
            </div>
        </div>

        {{-- Detail table below the visual timeline --}}
        <div class="mt-4">
            <h6 class="fw-700 mb-3 text-muted" style="font-size:.85rem;letter-spacing:.04em;text-transform:uppercase;">
                Detailed Movement Log
            </h6>
            <div class="table-responsive">
                <table class="portal-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Action</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Remarks</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($moves as $n => $move)
                        <tr>
                            <td class="text-muted">{{ $n + 1 }}</td>
                            <td>@include('partials.action-badge', ['action' => $move->action])</td>
                            <td>
                                <div class="fw-700">{{ $move->fromUser->name ?? 'System' }}</div>
                                <div class="text-muted fs-sm">{{ $move->fromDept->name ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="fw-700">{{ $move->toUser->name ?? '—' }}</div>
                                <div class="text-muted fs-sm">{{ $move->toDept->name ?? '—' }}</div>
                            </td>
                            <td class="text-muted fs-sm">{{ $move->remarks ?? '—' }}</td>
                            <td class="text-muted fs-sm">{{ $move->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @endif
    </div>
</div>
@endsection
