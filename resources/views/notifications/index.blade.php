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
            <i class="fa-solid fa-check-double"></i> Mark All Read ({{ $unread }})
        </button>
    </form>
    @endif
</div>

<div class="portal-card">
    <div class="card-body p-0">
        @forelse($notifications as $n)
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom {{ $n->read_at ? '' : 'notif-unread' }}">
            <div class="mt-1">
                <div class="topbar-icon-btn" style="background:{{ $n->read_at ? '#f8fafc' : '#dbeafe' }};color:{{ $n->read_at ? 'var(--muted)' : 'var(--primary)' }};">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="fw-700" style="font-size:.88rem;">{{ $n->data['message'] ?? 'Notification' }}</div>
                @if(!empty($n->data['file_title']))
                <div class="text-muted fs-sm">{{ $n->data['file_title'] }}</div>
                @endif
                <div class="text-muted fs-sm mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            @if(!$n->read_at)
            <span class="badge-status badge-approved" style="flex-shrink:0;">New</span>
            @endif
        </div>
        @empty
        <div class="empty-state py-5"><i class="fa-solid fa-bell-slash"></i>No notifications yet.</div>
        @endforelse
    </div>
</div>
@endsection
