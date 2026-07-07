<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Search for a government file by its File Number.">
    <title>Public File Search &mdash; FileTrack Office Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    <style>
        .search-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            padding: 60px 0 80px;
            color: #fff;
        }
        .search-hero h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: .5rem;
        }
        .search-hero p {
            opacity: .85;
            font-size: 1.05rem;
        }
        .search-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(30,58,95,.13);
            margin-top: -2.5rem;
            position: relative;
        }
        .result-card {
            background: #f0f7ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 12px;
            padding: 1.5rem 2rem;
        }
        .result-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0;
            border-bottom: 1px solid #dbeafe;
            font-size: .95rem;
        }
        .result-row:last-child { border-bottom: none; }
        .result-label { color: #4b5563; font-weight: 600; min-width: 160px; }
        .result-value { color: #1e3a5f; font-weight: 500; }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
        }
        .status-active         { background: #dcfce7; color: #166534; }
        .status-pending-transfer { background: #fef9c3; color: #854d0e; }
        .status-archived       { background: #f3f4f6; color: #374151; }
        .status-draft          { background: #e0e7ff; color: #3730a3; }
        .not-found-box {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            color: #b91c1c;
        }
    </style>
</head>

<body style="background:#f8fafc;">

    {{-- HEADER --}}
    <header class="site-header sticky-top" style="background:#fff;border-bottom:1px solid #e5e7eb;">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('welcome') }}">
                    <span class="brand-icon" style="background:#2563eb;color:#fff;border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-folder-tree"></i>
                    </span>
                    <span style="font-weight:700;font-size:1.1rem;">FileTrack Office</span>
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i>Home
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary px-3">
                        <i class="fa-solid fa-right-to-bracket me-1"></i>Login
                    </a>
                </div>
            </div>
        </nav>
    </header>

    {{-- HERO --}}
    <section class="search-hero">
        <div class="container text-center">
            <span style="font-size:.8rem;font-weight:600;letter-spacing:2px;text-transform:uppercase;opacity:.75;">Government File Tracking</span>
            <h1 class="mt-2"><i class="fa-solid fa-magnifying-glass me-2"></i>Public File Search</h1>
            <p>Enter the File Number to check the current status of any registered file.</p>
        </div>
    </section>

    {{-- SEARCH FORM + RESULTS --}}
    <div class="container" style="max-width:640px;padding-bottom:60px;">
        <div class="search-card">

            <form method="GET" action="{{ route('public.file.search.result') }}" novalidate>
                @csrf
                <label class="form-label fw-600 mb-1">File Number</label>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">
                        <i class="fa-solid fa-hashtag text-muted"></i>
                    </span>
                    <input
                        type="text"
                        name="file_number"
                        class="form-control @error('file_number') is-invalid @enderror"
                        placeholder="e.g. FILE-ABCD1234XY"
                        value="{{ old('file_number', request('file_number')) }}"
                        required
                        autocomplete="off"
                        style="font-size:1rem;">
                    <button type="submit" class="btn btn-primary px-4" style="font-weight:600;">
                        <i class="fa-solid fa-search me-1"></i> Search
                    </button>
                    @error('file_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="text-muted" style="font-size:.82rem;">
                    <i class="fa-solid fa-shield-halved me-1 text-primary"></i>
                    Only publicly available information is shown. No internal data is exposed.
                </div>
            </form>

            {{-- ERROR: file not found --}}
            @if(session('search_error'))
            <div class="not-found-box mt-4">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                {{ session('search_error') }}
            </div>
            @endif

            {{-- RESULT --}}
            @isset($result)
            <div class="mt-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-circle-check text-success fs-5"></i>
                    <span class="fw-700" style="font-size:1.05rem;">File Found</span>
                </div>
                <div class="result-card">
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-hashtag me-2 text-primary"></i>File Number</span>
                        <span class="result-value fw-700">{{ $result['file_number'] }}</span>
                    </div>
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-file-lines me-2 text-primary"></i>File Name</span>
                        <span class="result-value">{{ $result['file_name'] }}</span>
                    </div>
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-building-columns me-2 text-primary"></i>Department</span>
                        <span class="result-value">{{ $result['department'] }}</span>
                    </div>
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-user-check me-2 text-primary"></i>Current Holder</span>
                        <span class="result-value">{{ $result['current_holder'] }}</span>
                    </div>
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-circle-dot me-2 text-primary"></i>Current Status</span>
                        <span class="result-value">
                            @php
                                $statusKey = strtolower(str_replace(' ', '-', $result['status']));
                            @endphp
                            <span class="status-badge status-{{ $statusKey }}">{{ $result['status'] }}</span>
                        </span>
                    </div>
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-calendar me-2 text-primary"></i>Created Date</span>
                        <span class="result-value">{{ $result['created_date'] }}</span>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0" style="font-size:.82rem;">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    For more details, please contact the relevant department or login to the portal.
                </p>
            </div>
            @endisset

        </div>
    </div>

    {{-- FOOTER --}}
    <footer style="background:#1e3a5f;color:#94a3b8;text-align:center;padding:1.25rem;font-size:.85rem;">
        &copy; {{ date('Y') }} FileTrack Office Portal &mdash; Government File Tracking System
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
