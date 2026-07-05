<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FileTrack Office Portal') }} &mdash; @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @stack('styles')
</head>

<body class="portal-body">

    @auth
    @php
    $role = auth()->user()->role;
    $isSuper = $role === 'super_admin';
    $isAdmin = $role === 'admin';
    $isUser = $role === 'user';
    $dashRoute = $isSuper ? 'super_admin.dashboard' : ($isAdmin ? 'admin.dashboard' : 'user.dashboard');
    $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
    $latestNotifications = auth()->user()->notifications()->latest()->limit(15)->get()
        ->map(fn($notification) => \App\Support\NotificationPresenter::present($notification));
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

            <a href="{{ route('admin.files') }}"
                class="sidebar-link {{ request()->routeIs('admin.files*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open"></i><span>All Files</span>
            </a>

            <a href="{{ route('admin.transfers') }}"
                class="sidebar-link {{ request()->routeIs('admin.transfers') ? 'active' : '' }}">
                <i class="fa-solid fa-right-left"></i><span>Transfer History</span>
            </a>

            {{-- Admin: User Management (dept users only) --}}
            @if($isAdmin)
            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>User Management</span>
            </a>
            @endif

            <a href="{{ route('admin.designations.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.designations.*') ? 'active' : '' }}">
                <i class="fa-solid fa-id-badge"></i><span>Designations</span>
            </a>

            @if($isSuper)
            <a href="{{ route('admin.backup.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                <i class="fa-solid fa-database"></i><span>Backup</span>
            </a>
            @endif
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
                <i class="fa-solid fa-user-shield"></i><span>Admin Management</span>
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

    <div class="mobile-backdrop" id="sidebarBackdrop"></div>

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
                <div class="topbar-context d-none d-sm-flex">
                    <span class="topbar-context-label">Government File Tracking</span>
                    <span class="topbar-context-sub">Official workflow portal</span>
                </div>
                <nav aria-label="breadcrumb" class="d-none d-lg-block">
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
                    <button class="topbar-icon-btn" data-bs-toggle="dropdown" id="topbar-bell-btn" aria-label="Notifications">
                        <i class="fa-solid fa-bell"></i>
                        @if($unreadCount > 0)
                        <span class="topbar-badge" id="topbar-notif-badge">{{ $unreadCount }}</span>
                        @else
                        <span class="topbar-badge d-none" id="topbar-notif-badge"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" id="notif-dropdown-menu">
                        <div class="notif-header d-flex align-items-center gap-2 px-3 py-2">
                            <i class="fa-solid fa-bell text-primary"></i>
                            <strong>Notifications</strong>
                        </div>
                        <div class="notif-body" id="notif-dropdown-body">
                            @forelse($latestNotifications as $n)
                            <a href="{{ $n['url'] }}" class="notif-item {{ $n['is_unread'] ? 'notif-unread' : '' }}" data-notification-id="{{ $n['id'] }}" data-unread="{{ $n['is_unread'] ? '1' : '0' }}">
                                <span class="notif-icon notif-color-{{ $n['color'] }}"><i class="fa-solid fa-{{ $n['icon'] }}"></i></span>
                                <span class="notif-content">
                                    <span class="notif-title">{{ $n['title'] }}</span>
                                    <span class="notif-msg">{{ $n['message'] }}</span>
                                    <small class="text-muted">{{ $n['relative_time'] }}</small>
                                </span>
                            </a>
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

                        @if(auth()->user()->photo)
                        <img
                            src="{{ auth()->user()->photo_url }}"
                            alt="Profile"
                            class="topbar-avatar-img"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="topbar-avatar" style="display:none;">
                            {{ auth()->user()->initials }}
                        </div>
                        @else
                        <div class="topbar-avatar">
                            {{ auth()->user()->initials }}
                        </div>
                        @endif



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
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
        const sidebar = document.getElementById('portalSidebar');
        const mainArea = document.getElementById('portalMain');
        const toggleBtn = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebarBackdrop');

        function isMobileView() {
            return window.matchMedia('(max-width: 991px)').matches;
        }

        function syncSidebar() {
            if (!sidebar || !mainArea || !toggleBtn) return;

            if (isMobileView()) {
                sidebar.classList.remove('collapsed');
                mainArea.classList.remove('expanded');
                sidebar.classList.remove('mobile-open');
                if (backdrop) {
                    backdrop.classList.remove('show');
                }
                document.body.classList.remove('sidebar-open');
            } else {
                const shouldCollapse = localStorage.getItem('sidebarCollapsed') === 'true';
                sidebar.classList.toggle('collapsed', shouldCollapse);
                mainArea.classList.toggle('expanded', shouldCollapse);
                sidebar.classList.remove('mobile-open');
                if (backdrop) {
                    backdrop.classList.remove('show');
                }
                document.body.classList.remove('sidebar-open');
            }
        }

        if (sidebar && mainArea && toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                if (isMobileView()) {
                    sidebar.classList.toggle('mobile-open');
                    const isOpen = sidebar.classList.contains('mobile-open');
                    if (backdrop) {
                        backdrop.classList.toggle('show', isOpen);
                    }
                    document.body.classList.toggle('sidebar-open', isOpen);
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainArea.classList.toggle('expanded');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            });

            if (backdrop) {
                backdrop.addEventListener('click', () => {
                    sidebar.classList.remove('mobile-open');
                    backdrop.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                });
            }

            window.addEventListener('resize', syncSidebar);
            syncSidebar();
        }

        // ── Notification polling — Page Visibility aware ─────────────────
        (function() {
            const sound = document.getElementById('notif-sound');
            const topBadge = document.getElementById('topbar-notif-badge');
            const sbCount = document.getElementById('sb-notif-count');
            const bellBtn = document.getElementById('topbar-bell-btn');
            const dropdownBody = document.getElementById('notif-dropdown-body');
            const POLL_MS = 10000;
            const FIRST_MS = 1500;
            const csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
            let lastCount = parseInt(topBadge ? topBadge.textContent : '0', 10) || 0;
            let soundUnlocked = false;
            let pollTimer = null;
            let latestNotifications = @json($latestNotifications->values());

            ['click', 'keydown', 'touchstart'].forEach(function(ev) {
                document.addEventListener(ev, function() {
                    soundUnlocked = true;
                }, {
                    once: true
                });
            });

            function playSound() {
                if (sound && soundUnlocked) {
                    sound.currentTime = 0;
                    sound.play().catch(function() {});
                }
            }

            function setBadge(el, n) {
                if (!el) return;
                el.textContent = n > 0 ? n : '';
                if (n > 0) {
                    el.classList.remove('d-none');
                    el.classList.add('badge-bounce');
                    setTimeout(function() { el.classList.remove('badge-bounce'); }, 650);
                } else {
                    el.classList.add('d-none');
                }
            }

            function escapeHtml(value) {
                var div = document.createElement('div');
                div.textContent = value == null ? '' : String(value);
                return div.innerHTML;
            }

            function renderNotifications(items) {
                if (!dropdownBody) return;
                latestNotifications = items || [];

                if (!latestNotifications.length) {
                    dropdownBody.innerHTML = '<div class="notif-item text-muted text-center">No notifications</div>';
                    return;
                }

                dropdownBody.innerHTML = latestNotifications.map(function(item) {
                    return '<a href="' + escapeHtml(item.url || '#') + '" class="notif-item ' + (item.is_unread ? 'notif-unread' : '') + '"' +
                        ' data-notification-id="' + escapeHtml(item.id) + '" data-unread="' + (item.is_unread ? '1' : '0') + '">' +
                        '<span class="notif-icon notif-color-' + escapeHtml(item.color || 'gray') + '"><i class="fa-solid fa-' + escapeHtml(item.icon || 'bell') + '"></i></span>' +
                        '<span class="notif-content">' +
                        '<span class="notif-title">' + escapeHtml(item.title || 'Notification') + '</span>' +
                        '<span class="notif-msg">' + escapeHtml(item.message || 'New notification') + '</span>' +
                        '<small class="text-muted">' + escapeHtml(item.relative_time || '') + '</small>' +
                        '</span>' +
                        '</a>';
                }).join('');
            }

            function unreadVisibleIds() {
                if (!dropdownBody) return [];
                return Array.from(dropdownBody.querySelectorAll('[data-notification-id][data-unread="1"]'))
                    .slice(0, 15)
                    .map(function(el) { return el.dataset.notificationId; });
            }

            function markVisibleAsRead() {
                var ids = unreadVisibleIds();
                if (!ids.length) return;

                latestNotifications = latestNotifications.map(function(item) {
                    if (ids.indexOf(item.id) !== -1) {
                        item.is_unread = false;
                        item.read_at = new Date().toISOString();
                    }
                    return item;
                });
                renderNotifications(latestNotifications);
                lastCount = Math.max(0, lastCount - ids.length);
                setBadge(topBadge, lastCount);
                setBadge(sbCount, lastCount);

                fetch('{{ route("notifications.readVisible") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ ids: ids })
                })
                    .then(function(r) { return r.ok ? r.json() : null; })
                    .then(function(data) {
                        if (!data) return;
                        lastCount = data.unread_count || 0;
                        setBadge(topBadge, lastCount);
                        setBadge(sbCount, lastCount);
                    })
                    .catch(function() {});
            }
                if (document.hidden) return;
                fetch('{{ route("notifications.poll") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    })
                    .then(function(r) {
                        return r.ok ? r.json() : null;
                    })
                    .then(function(data) {
                        if (!data) return;
                        var n = data.unread_count || 0;
                        if (n > lastCount) { playSound(); }
                        lastCount = n;
                        setBadge(topBadge, n);
                        setBadge(sbCount, n);
                        renderNotifications(data.notifications || []);
                        if (bellBtn && bellBtn.getAttribute('aria-expanded') === 'true') {
                            markVisibleAsRead();
                        }
                    })
                    .catch(function() {});
            }

            function startPolling() {
                if (!pollTimer) pollTimer = setInterval(poll, POLL_MS);
            }

            function stopPolling() {
                clearInterval(pollTimer);
                pollTimer = null;
            }

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopPolling();
                } else {
                    poll();
                    startPolling();
                }
            });

            if (bellBtn) {
                bellBtn.addEventListener('shown.bs.dropdown', markVisibleAsRead);
            }

            // ── Single notification click: mark read, then navigate ───────
            if (dropdownBody) {
                dropdownBody.addEventListener('click', function(e) {
                    var link = e.target.closest('a[data-notification-id]');
                    if (!link) return;
                    if (link.dataset.unread !== '1') return; // already read — let <a> navigate normally
                    e.preventDefault();
                    var id  = link.dataset.notificationId;
                    var url = link.getAttribute('href');

                    // Optimistically update UI
                    link.dataset.unread = '0';
                    link.classList.remove('notif-unread');
                    lastCount = Math.max(0, lastCount - 1);
                    setBadge(topBadge, lastCount);
                    setBadge(sbCount, lastCount);

                    fetch('{{ route("notifications.readVisible") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ ids: [id] })
                    })
                    .then(function(r) { return r.ok ? r.json() : null; })
                    .then(function(data) {
                        if (data) {
                            lastCount = data.unread_count || 0;
                            setBadge(topBadge, lastCount);
                            setBadge(sbCount, lastCount);
                        }
                    })
                    .catch(function() {})
                    .finally(function() { if (url && url !== '#') window.location.href = url; });
                });
            }

            setTimeout(function() {
                poll();
                startPolling();
            }, FIRST_MS);
        })();
    </script>
    @stack('scripts')
</body>

</html>
