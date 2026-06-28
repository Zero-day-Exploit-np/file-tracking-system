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
        :root {
            --primary: #17406b;
            --primary-dark: #103154;
            --bg: #eef3f7;
            --surface: #ffffff;
            --border: #dce3ea;
            --text: #17212b;
            --muted: #5f6f7f;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, var(--bg) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 54px 20px 24px;
        }
        .auth-wrapper { width: 100%; max-width: 460px; }
        .auth-brand { text-align: center; margin-bottom: 24px; }
        .auth-brand-icon {
            width: 54px; height: 54px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.4rem; margin-bottom: 12px;
            box-shadow: 0 10px 24px rgba(23, 64, 107, .18);
        }
        .auth-brand-name { font-size: 1.3rem; font-weight: 800; color: var(--text); display: block; }
        .auth-brand-sub { font-size: .82rem; color: var(--muted); }
        .auth-card {
            background: var(--surface);
            border-radius: 18px;
            padding: 30px 28px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, .08);
            border: 1px solid var(--border);
        }
        .auth-card h2 { font-size: 1.15rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .auth-card .auth-sub { font-size: .82rem; color: var(--muted); margin-bottom: 24px; }
        .form-label { font-size: .845rem; font-weight: 600; color: var(--text); margin-bottom: 5px; }
        .form-control { border-radius: 8px; border-color: var(--border); font-size: .875rem; padding: 9px 12px; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23, 64, 107, .12); }
        .btn-auth { background: var(--primary); color: #fff; border: none; width: 100%; padding: 10px; border-radius: 8px; font-weight: 700; font-size: .9rem; margin-top: 4px; transition: background .15s; }
        .btn-auth:hover { background: var(--primary-dark); color: #fff; }
        .auth-footer { text-align: center; margin-top: 20px; font-size: .82rem; color: var(--muted); }
        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
        .alert-auth { border-radius: 10px; font-size: .845rem; margin-bottom: 16px; }
        .input-group-text { background: #f8fafc; border-color: var(--border); }
        .gov-banner {
            background: linear-gradient(90deg, #102a43 0%, #153a5d 100%);
            color: #dbe5ef;
            text-align: center;
            padding: 8px 16px;
            font-size: .75rem;
            letter-spacing: .03em;
            position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
        }
        .gov-banner strong { color: #fff; }
        @media (max-width: 576px) {
            body { padding: 44px 12px 16px; }
            .auth-card { padding: 24px 18px; }
        }
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
