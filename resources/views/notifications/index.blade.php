@extends('layouts.app')
@section('title', 'Notifications')

@section('breadcrumb')
<li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Notifications</h1>
        <div class="page-subtitle">Your latest alerts and system updates</div>
    </div>
    @php $unread = $notifications->whereNull('read_at')->count(); @endphp
    @if($unread > 0)
    <form method="POST" action="{{ route('notifications.readAll') }}">
        @csrf
        <button type="submit" class="btn-portal-primary">
            <i class="fa-solid fa-check-double me-1"></i>Mark All Read
            <span class="badge bg-white text-primary ms-1">{{ $unread }}</span>
        </button>
    </form>
    @endif
</div>

<div class="portal-card">
    <div class="card-body p-0">
        @forelse($notifications as $n)
        @php
            $type    = $n->data['type'] ?? 'info';
            $isUnread = !$n->read_at;

            $iconMap = [
                'transfer_requested' => ['fa-paper-plane',   '#fff3cd', '#856404'],
                'transfer_approved'  => ['fa-check-circle',  '#d1e7dd', '#0f5132'],
                'transfer_rejected'  => ['fa-times-circle',  '#f8d7da', '#842029'],
                'file_transferred'   => ['fa-right-left',    '#cfe2ff', '#084298'],
                'info'               => ['fa-bell',           '#f8fafc', '#64748b'],
            ];
            $iconData = $iconMap[$type] ?? $iconMap['info'];
        @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom {{ $isUnread ? 'notif-unread' : '' }}">
            {{-- Icon --}}
            <div class="mt-1 flex-shrink-0">
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $iconData[1] }};
                            color:{{ $iconData[2] }};display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid {{ $iconData[0] }}"></i>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-grow-1">
                <div class="fw-700" style="font-size:.88rem;">
                    {{ $n->data['message'] ?? 'Notification' }}
                </div>

                @if(!empty($n->data['file_title']))
                <div class="text-muted fs-sm mt-1">
                    <i class="fa-solid fa-file-lines me-1"></i>{{ $n->data['file_title'] }}
                    @if(!empty($n->data['file_number']))
                    &mdash; <code>{{ $n->data['file_number'] }}</code>
                    @endif
                </div>
                @endif

                @if(!empty($n->data['from_dept']) && !empty($n->data['to_dept']))
                <div class="text-muted fs-sm">
                    <i class="fa-solid fa-arrow-right me-1"></i>
                    {{ $n->data['from_dept'] }} &rarr; {{ $n->data['to_dept'] }}
                </div>
                @endif

                <div class="text-muted fs-sm mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>

            {{-- Status badge --}}
            <div class="flex-shrink-0">
                @if($isUnread)
                <span class="badge-status badge-approved">New</span>
                @endif

                @if($type === 'transfer_approved')
                <span class="badge-status badge-active"><i class="fa-solid fa-check me-1"></i>Approved</span>
                @elseif($type === 'transfer_rejected')
                <span class="badge-status badge-rejected"><i class="fa-solid fa-xmark me-1"></i>Rejected</span>
                @elseif($type === 'transfer_requested')
                <span class="badge-status badge-pending"><i class="fa-solid fa-clock me-1"></i>Pending</span>
                @elseif($type === 'file_transferred')
                <span class="badge-status badge-transferred"><i class="fa-solid fa-right-left me-1"></i>Received</span>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state py-5">
            <i class="fa-solid fa-bell-slash"></i>
            No notifications yet.
        </div>
        @endforelse
    </div>
</div>
@endsection
