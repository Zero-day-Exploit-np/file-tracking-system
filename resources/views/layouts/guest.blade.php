<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FileTrack Office Portal &mdash; @yield('title', 'Login')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0d6efd; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-wrapper { width: 100%; max-width: 440px; padding: 20px; }
        .auth-brand { text-align: center; margin-bottom: 28px; }
        .auth-brand-icon { width: 52px; height: 52px; background: var(--primary); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; margin-bottom: 12px; }
        .auth-brand-name { font-size: 1.3rem; font-weight: 800; color: #1e293b; display: block; }
        .auth-brand-sub { font-size: .8rem; color: #64748b; }
        .auth-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px rgba(0,0,0,.08); border: 1px solid #e2e8f0; }
        .auth-card h2 { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .auth-card .auth-sub { font-size: .82rem; color: #64748b; margin-bottom: 24px; }
        .form-label { font-size: .845rem; font-weight: 600; color: #1e293b; margin-bottom: 5px; }
        .form-control { border-radius: 8px; border-color: #e2e8f0; font-size: .875rem; padding: 9px 12px; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(13,110,253,.12); }
        .btn-auth { background: var(--primary); color: #fff; border: none; width: 100%; padding: 10px; border-radius: 8px; font-weight: 700; font-size: .9rem; margin-top: 4px; transition: background .15s; }
        .btn-auth:hover { background: #0a58ca; color: #fff; }
        .auth-footer { text-align: center; margin-top: 20px; font-size: .82rem; color: #64748b; }
        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
        .alert-auth { border-radius: 8px; font-size: .845rem; margin-bottom: 16px; }
        .input-group-text { background: #f8fafc; border-color: #e2e8f0; }
        .gov-banner { background: #1f2937; color: #d1d5db; text-align: center; padding: 8px 16px; font-size: .75rem; letter-spacing: .03em; position: fixed; top: 0; left: 0; right: 0; z-index: 9999; }
        .gov-banner strong { color: #fff; }
        body { padding-top: 38px; }
    </style>
</head>
<body>
    <div class="gov-banner">
        <strong>OFFICIAL GOVERNMENT PORTAL</strong> &mdash; This is an official file tracking and management system. Unauthorized access is prohibited.
    </div>

    <div class="auth-wrapper">
        <div class="auth-brand">
            <div class="auth-brand-icon"><i class="fa-solid fa-folder-tree"></i></div>
            <span class="auth-brand-name">FileTrack Office</span>
            <span class="auth-brand-sub">Government File Tracking System</span>
        </div>
        <div class="auth-card">
            {{ $slot }}
        </div>
        <div class="auth-footer">
            <a href="{{ route('welcome') }}"><i class="fa-solid fa-arrow-left me-1"></i>Back to Portal</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
