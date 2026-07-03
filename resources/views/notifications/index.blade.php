@extends('layouts.app')
@section('title', 'Notifications')

@section('breadcrumb')
<li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Notifications</h1>
        <div class="page-subtitle">Your latest alerts and updates</div>
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
            $type     = $n->data['type'] ?? 'info';
            $isUnread = !$n->read_at;

            $iconMap = [
                'file_received'    => ['fa-inbox',     '#cfe2ff', '#084298'],
                'file_transferred' => ['fa-right-left','#cfe2ff', '#084298'],
                'info'             => ['fa-bell',       '#f8fafc', '#64748b'],
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

                @if(!empty($n->data['sender']))
                <div class="text-muted fs-sm">
                    <i class="fa-solid fa-user me-1"></i>From: {{ $n->data['sender'] }}
                </div>
                @endif

                <div class="text-muted fs-sm mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>

            {{-- Status badge --}}
            <div class="flex-shrink-0">
                @if($isUnread)
                <span class="badge-status badge-active">New</span>
                @endif
                @if(in_array($type, ['file_received', 'file_transferred']))
                <span class="badge-status badge-transferred"><i class="fa-solid fa-inbox me-1"></i>Received</span>
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
