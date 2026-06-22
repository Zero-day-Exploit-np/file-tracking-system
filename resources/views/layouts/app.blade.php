<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FileTrack') }} &mdash; @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @stack('styles')
</head>
<body class="portal-body">

@auth
@php
    $role      = auth()->user()->role;
    $isSuper   = $role === 'super_admin';
    $isAdmin   = $role === 'admin';
    $isUser    = $role === 'user';
    $deptId    = auth()->user()->department_id;

    // Dashboard route per role
    $dashRoute = $isSuper ? 'super_admin.dashboard' : ($isAdmin ? 'admin.dashboard' : 'user.dashboard');

    // Pending badge (only for admin — they can act on it)
    $pendingCount = $isAdmin
        ? \App\Models\TransferRequest::where('status','pending')->where('to_department', $deptId)->count()
        : 0;

    $unreadCount = auth()->user()->unreadNotifications->count();
@endphp

<!-- ================================================================
     SIDEBAR
================================================================ -->
<div class="portal-sidebar" id="portalSidebar">
    <div class="sidebar-brand">
        <div class="brand-icon-wrap"><i class="fa-solid fa-folder-tree"></i></div>
        <div class="brand-text">
            <span class="brand-name">FileTrack</span>
            <span class="brand-sub">Office Portal</span>
        </div>
    </div>

    <nav class="sidebar-nav">

        {{-- ── COMMON ─────────────────────────────────────── --}}
        <div class="nav-section-label">Main</div>

        <a href="{{ route($dashRoute) }}"
           class="sidebar-link {{ request()->routeIs($dashRoute) || request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i><span>Dashboard</span>
        </a>

        <a href="{{ route('files.index') }}"
           class="sidebar-link {{ request()->routeIs('files.index','files.show','files.create') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i>
            <span>{{ $isUser ? 'My Files' : 'Files' }}</span>
        </a>

        <a href="{{ route('notifications.index') }}"
           class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fa-solid fa-bell"></i><span>Notifications</span>
            @if($unreadCount > 0)
            <span class="sidebar-badge" id="sb-notif-count">{{ $unreadCount }}</span>
            @else
            <span class="sidebar-badge d-none" id="sb-notif-count"></span>
            @endif
        </a>

        {{-- ── ADMIN SECTION ───────────────────────────────── --}}
        @if($isAdmin || $isSuper)
        <div class="nav-section-label mt-2">Administration</div>

        <a href="{{ route('admin.transfer.requests') }}"
           class="sidebar-link {{ request()->routeIs('admin.transfer.*') ? 'active' : '' }}">
            <i class="fa-solid fa-right-left"></i><span>Transfer Requests</span>
            @if($pendingCount > 0)
            <span class="sidebar-badge" id="sb-transfer-count">{{ $pendingCount }}</span>
            @else
            <span class="sidebar-badge d-none" id="sb-transfer-count"></span>
            @endif
        </a>

        <a href="{{ route('admin.files') }}"
           class="sidebar-link {{ request()->routeIs('admin.files*') ? 'active' : '' }}">
            <i class="fa-solid fa-folder-open"></i><span>All Files</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i><span>Users</span>
        </a>

        <a href="{{ route('admin.designations.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.designations.*') ? 'active' : '' }}">
            <i class="fa-solid fa-id-badge"></i><span>Designations</span>
        </a>

        <a href="{{ route('admin.public-files.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.public-files.*') ? 'active' : '' }}">
            <i class="fa-solid fa-cloud-arrow-up"></i><span>Public Uploads</span>
        </a>

        <a href="{{ route('admin.audit.logs') }}"
           class="sidebar-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i><span>Audit Logs</span>
        </a>
        @endif

        {{-- ── SUPER ADMIN ONLY ────────────────────────────── --}}
        @if($isSuper)
        <div class="nav-section-label mt-2">System</div>

        <a href="{{ route('departments.index') }}"
           class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building-columns"></i><span>Departments</span>
        </a>

        <a href="{{ route('users.index') }}"
           class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i><span>Admin Users</span>
        </a>
        @endif

        {{-- ── ACCOUNT ─────────────────────────────────────── --}}
        <div class="nav-section-label mt-2">Account</div>

        <a href="{{ route('profile.edit') }}"
           class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-pen"></i><span>Profile</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="sidebar-link sidebar-logout">
                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
            </button>
        </form>

    </nav>
</div>
{{-- END SIDEBAR --}}

<!-- ================================================================
     MAIN AREA
================================================================ -->
<div class="portal-main" id="portalMain">

    <!-- TOP NAVBAR -->
    <header class="portal-topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route($dashRoute) }}">Home</a>
                    </li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>

        <div class="topbar-right">
            {{-- Notification Bell --}}
            <div class="dropdown me-2">
                <button class="topbar-icon-btn" data-bs-toggle="dropdown" id="topbar-bell-btn">
                    <i class="fa-solid fa-bell"></i>
                    @if($unreadCount > 0)
                    <span class="topbar-badge" id="topbar-notif-badge">{{ $unreadCount }}</span>
                    @else
                    <span class="topbar-badge d-none" id="topbar-notif-badge"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0">
                    <div class="notif-header d-flex justify-content-between align-items-center px-3 py-2">
                        <strong>Notifications</strong>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.readAll') }}">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0">Mark all read</button>
                        </form>
                        @endif
                    </div>
                    <div class="notif-body" id="notif-dropdown-body">
                        @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $n)
                        <div class="notif-item {{ $n->read_at ? '' : 'notif-unread' }}">
                            <div class="notif-msg">{{ $n->data['message'] ?? 'Notification' }}</div>
                            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                        </div>
                        @empty
                        <div class="notif-item text-muted text-center">No notifications</div>
                        @endforelse
                    </div>
                    <div class="notif-footer text-center py-2">
                        <a href="{{ route('notifications.index') }}" class="small">View all notifications</a>
                    </div>
                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="dropdown">
                <button class="topbar-user-btn" data-bs-toggle="dropdown">
                    <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="d-none d-md-block text-start">
                        <div class="topbar-user-name">{{ auth()->user()->name }}</div>
                        <div class="topbar-user-role">{{ ucfirst(str_replace('_',' ', $role)) }}</div>
                    </div>
                    <i class="fa-solid fa-chevron-down ms-1 small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-pen me-2"></i>Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    {{-- END TOPBAR --}}

    <!-- PAGE CONTENT -->
    <main class="portal-content">

        {{-- Global Flash Alerts --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show portal-alert" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show portal-alert" role="alert">
            <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show portal-alert" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="portal-footer">
        <span>&copy; {{ date('Y') }} FileTrack Office Portal &mdash; Government File Tracking System</span>
    </footer>
</div>
{{-- END MAIN --}}

@endauth

<!-- ================================================================
     NOTIFICATION SOUND (hidden audio element)
================================================================ -->
<audio id="notif-sound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar toggle ────────────────────────────────────────────────
const sidebar   = document.getElementById('portalSidebar');
const mainArea  = document.getElementById('portalMain');
const toggleBtn = document.getElementById('sidebarToggle');

if (sidebar && mainArea && toggleBtn) {
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        mainArea.classList.add('expanded');
    }
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainArea.classList.toggle('expanded');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
}

// ── Notification polling (30 s interval) ─────────────────────────
(function () {
    const sound        = document.getElementById('notif-sound');
    const topBadge     = document.getElementById('topbar-notif-badge');
    const sbCount      = document.getElementById('sb-notif-count');
    const sbTransfer   = document.getElementById('sb-transfer-count');
    let lastCount      = parseInt(topBadge?.textContent || '0', 10);
    let soundUnlocked  = false;

    // Unlock audio on first user interaction
    document.addEventListener('click', () => { soundUnlocked = true; }, { once: true });
    document.addEventListener('keydown', () => { soundUnlocked = true; }, { once: true });

    function playSound() {
        if (sound && soundUnlocked) {
            sound.currentTime = 0;
            sound.play().catch(() => {});
        }
    }

    function updateBadge(el, count) {
        if (!el) return;
        if (count > 0) {
            el.textContent = count;
            el.classList.remove('d-none');
        } else {
            el.classList.add('d-none');
        }
    }

    function poll() {
        fetch('{{ route("notifications.poll") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            const newCount = data.unread_count;

            if (newCount > lastCount) {
                playSound();
            }
            lastCount = newCount;

            updateBadge(topBadge, newCount);
            updateBadge(sbCount, newCount);
        })
        .catch(() => {});
    }

    // Start polling every 30 seconds
    setTimeout(poll, 5000);      // first poll after 5 s
    setInterval(poll, 30000);    // then every 30 s
})();
</script>
@stack('scripts')
</body>
</html>
