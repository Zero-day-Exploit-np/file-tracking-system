@extends('layouts.app')
@section('title', 'File Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">{{ $file->file_number }}</li>
@endsection

@push('styles')
<style>
/* ── Horizontal Timeline ─────────────────────────────────────────── */
.htl-scroll-wrap { overflow-x: auto; padding-bottom: .5rem; }
.htl {
    display: flex;
    align-items: flex-start;
    gap: 0;
    min-width: max-content;
    padding: 1.5rem 1rem 1rem;
}

/* Connector arrow */
.htl-connector {
    width: 56px; height: 2px;
    background: linear-gradient(90deg,#17406b 0%,#4f8fcd 100%);
    align-self: center;
    flex-shrink: 0;
    position: relative;
}
.htl-connector::after {
    content: '';
    position: absolute;
    right: -7px; top: -5px;
    border: 6px solid transparent;
    border-left-color: #4f8fcd;
}

/* Timeline card */
.htl-card {
    width: 160px;
    background: #fff;
    border: 1.5px solid #dce3ea;
    border-radius: 12px;
    padding: 14px 12px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    box-shadow: 0 2px 8px rgba(15,23,42,.06);
    position: relative;
    transition: border-color .15s, box-shadow .15s;
}
.htl-card:hover {
    border-color: #17406b;
    box-shadow: 0 4px 16px rgba(23,64,107,.12);
}
.htl-card.card-created  { border-top: 3px solid #7c3aed; }
.htl-card.card-transfer { border-top: 3px solid #17406b; }
.htl-card.card-dept     { border-top: 3px solid #059669; }
.htl-card.card-current  { border-top: 3px solid #f59e0b; border-color: #f59e0b; }

/* Avatar inside card */
.htl-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 8px;
    border: 2px solid #e2e8f0;
    flex-shrink: 0;
}
.htl-avatar-initials {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 800; color: #fff;
    margin-bottom: 8px; border: 2px solid #e2e8f0; flex-shrink: 0;
}
.htl-avatar-initials.bg-created  { background: #7c3aed; }
.htl-avatar-initials.bg-transfer { background: #17406b; }
.htl-avatar-initials.bg-dept     { background: #059669; }

.htl-card .name { font-size: .82rem; font-weight: 700; color: #1e293b; word-break: break-word; line-height: 1.3; }
.htl-card .dept { font-size: .70rem; color: #64748b; margin-top: 2px; word-break: break-word; }
.htl-card .dt   { font-size: .68rem; color: #94a3b8; margin-top: 4px; line-height: 1.4; }
.htl-card .remark {
    display: inline-block;
    background: #f1f5f9; border-radius: 4px;
    padding: 2px 6px; font-size: .65rem; color: #64748b;
    margin-top: 5px; word-break: break-word;
    max-width: 136px; text-align: left;
}
.htl-badge-current {
    position: absolute; top: -10px; left: 50%; transform: translateX(-50%);
    background: #f59e0b; color: #fff;
    font-size: .6rem; font-weight: 700; padding: 2px 8px; border-radius: 999px;
    white-space: nowrap;
}

/* Mobile: vertical */
@media (max-width: 600px) {
    .htl { flex-direction: column; align-items: flex-start; min-width: unset; padding: .75rem .25rem; }
    .htl-connector { width: 2px; height: 36px; background: linear-gradient(180deg,#17406b 0%,#4f8fcd 100%); align-self: flex-start; margin-left: 21px; }
    .htl-connector::after { right: unset; top: unset; bottom: -7px; left: -5px; border-left-color: transparent; border-top-color: #4f8fcd; }
    .htl-card { width: calc(100vw - 80px); max-width: 300px; flex-direction: row; align-items: flex-start; text-align: left; gap: 10px; padding: 10px; }
    .htl-card .name, .htl-card .dept, .htl-card .dt, .htl-card .remark { max-width: unset; }
    .htl-avatar, .htl-avatar-initials { margin-bottom: 0; flex-shrink: 0; }
    .htl-badge-current { top: -10px; }
}
</style>
@endpush

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
        <a href="{{ route('files.download', $file->uuid) }}" class="btn btn-success">
            <i class="fa-solid fa-download"></i> Download
        </a>
        @endif
        @can('transfer', $file)
        <a href="{{ route('files.transfer.create', $file->uuid) }}" class="btn-portal-primary">
            <i class="fa-solid fa-right-left"></i> Transfer
        </a>
        @endcan
    </div>
</div>

{{-- FILE INFO CARDS --}}
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
                        <div class="d-flex align-items-center gap-2">
                            @if($file->currentHolder)
                                @if($file->currentHolder->photo_url)
                                <img src="{{ $file->currentHolder->photo_url }}" alt="{{ $file->currentHolder->name }}"
                                     style="width:24px;height:24px;border-radius:50%;object-fit:cover;">
                                @else
                                <div style="width:24px;height:24px;border-radius:50%;background:#dbeafe;color:#2563eb;
                                            display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;">
                                    {{ $file->currentHolder->initials }}
                                </div>
                                @endif
                                <span>{{ $file->currentHolder->name }}</span>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </div>
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
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-paperclip text-muted"></i>
                            <span>{{ $file->attachment_name }}</span>
                            @can('download', $file)
                            <a href="{{ route('files.download', $file->uuid) }}" class="btn btn-sm btn-outline-success">
                                <i class="fa-solid fa-download me-1"></i>Download
                            </a>
                            @endcan
                        </div>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-sm">Origin Dept.</span>
                        <span class="fw-700">{{ $file->movements->first()?->fromDept?->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MOVEMENT TIMELINE --}}
<div class="portal-card">
    <div class="card-header"><i class="fa-solid fa-route me-2 text-primary"></i>File Journey</div>
    <div class="card-body">
        @php $moves = $file->movements->sortBy('created_at'); @endphp
        @if($moves->isEmpty())
        <div class="empty-state"><i class="fa-solid fa-timeline"></i>No movement history available.</div>
        @else
        <div class="htl-scroll-wrap">
            <div class="htl">
                @foreach($moves as $i => $move)
                @php
                    $isCreated  = $move->action === 'created';
                    $isDeptMove = $move->fromDept && $move->toDept
                                  && $move->fromDept->id !== $move->toDept->id;
                    $isCurrent  = $move === $moves->last();

                    $cardClass = $isCreated ? 'card-created' : ($isDeptMove ? 'card-dept' : 'card-transfer');
                    if ($isCurrent) $cardClass .= ' card-current';

                    // Who to show on this card
                    if ($isCreated) {
                        $person    = $move->fromUser;
                        $deptLabel = $move->fromDept?->name ?? '—';
                        $avatarBg  = 'bg-created';
                    } else {
                        $person    = $move->toUser;
                        $deptLabel = $move->toDept?->name ?? '—';
                        $avatarBg  = $isDeptMove ? 'bg-dept' : 'bg-transfer';
                    }
                @endphp

                @if($i > 0)
                <div class="htl-connector"></div>
                @endif

                <div class="htl-card {{ $cardClass }}">
                    @if($isCurrent)
                    <span class="htl-badge-current">Current Holder</span>
                    @endif

                    {{-- Avatar --}}
                    @if($person && $person->photo_url)
                    <img src="{{ $person->photo_url }}" alt="{{ $person->name }}" class="htl-avatar">
                    @elseif($person)
                    <div class="htl-avatar-initials {{ $avatarBg }}">{{ $person->initials }}</div>
                    @else
                    <div class="htl-avatar-initials bg-dept">
                        <i class="fa-solid fa-building fa-sm"></i>
                    </div>
                    @endif

                    <div class="name">{{ $person?->name ?? $deptLabel }}</div>
                    <div class="dept">{{ $deptLabel }}</div>
                    <div class="dt">
                        {{ $move->created_at->format('d M Y') }}<br>
                        {{ $move->created_at->format('h:i A') }}
                    </div>
                    @if($move->remarks)
                    <div class="remark" title="{{ $move->remarks }}">
                        <i class="fa-solid fa-quote-left fa-xs me-1"></i>{{ Str::limit($move->remarks, 35) }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
