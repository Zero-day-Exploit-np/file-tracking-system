@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Notifications</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">Mark all as read</button>
        </form>
    </div>

    <div class="list-group">
        @forelse($notifications as $notification)
        <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $notification->data['message'] ?? 'Notification' }}</strong>
                    <p class="mb-1">{{ $notification->data['file_title'] ?? '' }}</p>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                @if(!$notification->read_at)
                <span class="badge bg-primary">New</span>
                @endif
            </div>
        </div>
        @empty
        <div class="alert alert-info">No notifications yet.</div>
        @endforelse
    </div>
</div>

@endsection