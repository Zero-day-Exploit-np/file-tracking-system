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

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; }

        /* ── Hero ───────────────────────────────────── */
        .search-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            padding: 56px 0 76px;
            color: #fff;
        }
        .search-hero h1 { font-size: 2rem; font-weight: 800; margin-bottom: .4rem; }
        .search-hero p  { opacity: .85; font-size: 1rem; }

        /* ── Main card ──────────────────────────────── */
        .search-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(30,58,95,.13);
            margin-top: -2.5rem;
            position: relative;
        }

        /* ── File info result ───────────────────────── */
        .result-card {
            background: #f0f7ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
        }
        .result-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .45rem 0;
            border-bottom: 1px solid #dbeafe;
            font-size: .92rem;
        }
        .result-row:last-child { border-bottom: none; }
        .result-label { color: #4b5563; font-weight: 600; min-width: 150px; }
        .result-value { color: #1e3a5f; font-weight: 500; }

        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 700;
        }
        .status-active           { background: #dcfce7; color: #166534; }
        .status-pending-transfer { background: #fef9c3; color: #854d0e; }
        .status-archived         { background: #f3f4f6; color: #374151; }
        .status-draft            { background: #e0e7ff; color: #3730a3; }

        .not-found-box {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 12px;
            padding: 1.1rem 1.4rem;
            color: #b91c1c;
            font-size: .9rem;
        }

        /* ══════════════════════════════════════════════
           PUBLIC FILE JOURNEY — horizontal linked-list
           Collapses all internal movements to dept nodes.
           Desktop: horizontal scroll  |  Mobile: vertical
           ══════════════════════════════════════════════ */
        .pjt-section {
            margin-top: 2rem;
            border-top: 1.5px solid #dbeafe;
            padding-top: 1.5rem;
        }
        .pjt-title {
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #1e3a5f;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Outer scroll wrapper */
        .pjt-scroll {
            overflow-x: auto;
            overflow-y: visible;
            padding-bottom: .5rem;
            scrollbar-width: thin;
            scrollbar-color: #bfdbfe transparent;
        }
        .pjt-scroll::-webkit-scrollbar { height: 4px; }
        .pjt-scroll::-webkit-scrollbar-thumb { background: #bfdbfe; border-radius: 999px; }

        /* Flex track */
        .pjt-track {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 0;
            min-width: max-content;
            padding: 1rem .25rem .5rem;
        }

        /* Arrow connector */
        .pjt-arrow {
            display: flex;
            align-items: center;
            align-self: center;
            flex-shrink: 0;
            margin-top: -16px; /* nudge to align with card centre */
        }
        .pjt-arrow-line {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, #93c5fd 0%, #3b82f6 100%);
            position: relative;
        }
        .pjt-arrow-line::after {
            content: '';
            position: absolute;
            right: -6px; top: -4px;
            border: 5px solid transparent;
            border-left-color: #3b82f6;
        }

        /* Card */
        .pjt-card {
            width: 170px;
            background: #fff;
            border: 1.5px solid #dbeafe;
            border-radius: 14px;
            border-top: 4px solid #3b82f6;
            padding: 14px 14px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(30,58,95,.07);
            position: relative;
            transition: box-shadow .18s, transform .18s;
        }
        .pjt-card:hover {
            box-shadow: 0 6px 22px rgba(37,99,235,.14);
            transform: translateY(-3px);
        }

        /* Current dept — green highlight */
        .pjt-card-current {
            border-top-color: #22c55e;
            border-color: #bbf7d0;
            background: #f0fdf4;
        }
        .pjt-card-current:hover { box-shadow: 0 6px 22px rgba(34,197,94,.18); }

        /* Current badge */
        .pjt-current-badge {
            position: absolute;
            top: -12px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(90deg, #16a34a, #22c55e);
            color: #fff;
            font-size: .6rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 2px 10px;
            border-radius: 999px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(22,163,74,.28);
        }

        /* Department icon circle */
        .pjt-icon {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(37,99,235,.22);
        }
        .pjt-card-current .pjt-icon {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            box-shadow: 0 2px 8px rgba(22,163,74,.28);
        }

        .pjt-dept   { font-size: .84rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; word-break: break-word; line-height: 1.3; }
        .pjt-action { font-size: .7rem; font-weight: 700; color: #2563eb; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .04em; }
        .pjt-card-current .pjt-action { color: #16a34a; }

        .pjt-date {
            font-size: .68rem; color: #94a3b8; margin-bottom: 3px;
            display: flex; align-items: center; justify-content: center; gap: 3px;
        }
        .pjt-remark {
            font-size: .67rem;
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 7px;
            margin-top: 6px;
            text-align: left;
            width: 100%;
            word-break: break-word;
            line-height: 1.4;
        }
        .pjt-card-current .pjt-remark { background: rgba(255,255,255,.7); border-color: #bbf7d0; }

        /* Mobile: vertical stack */
        @media (max-width: 640px) {
            .pjt-scroll { overflow-x: visible; }
            .pjt-track {
                flex-direction: column;
                align-items: stretch;
                min-width: unset;
                padding: .5rem 0;
            }
            .pjt-arrow {
                flex-direction: column;
                align-items: center;
                align-self: flex-start;
                margin-top: 0;
                margin-left: 23px;
                padding: 2px 0;
            }
            .pjt-arrow-line {
                width: 2px; height: 28px;
                background: linear-gradient(180deg, #93c5fd 0%, #3b82f6 100%);
            }
            .pjt-arrow-line::after {
                right: unset; top: unset;
                bottom: -5px; left: -4px;
                border-left-color: transparent;
                border-top-color: #3b82f6;
            }
            .pjt-card {
                width: 100%;
                flex-direction: row;
                align-items: flex-start;
                text-align: left;
                gap: 12px;
                padding: 12px;
            }
            .pjt-icon { margin-bottom: 0; flex-shrink: 0; }
            .pjt-date, .pjt-action { justify-content: flex-start; }
            .pjt-current-badge { left: 14px; transform: none; top: -10px; }
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <header class="sticky-top" style="background:#fff;border-bottom:1px solid #e5e7eb;">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('welcome') }}">
                    <span style="background:#2563eb;color:#fff;border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
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
            <span style="font-size:.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;opacity:.7;">
                Government File Tracking
            </span>
            <h1 class="mt-2"><i class="fa-solid fa-magnifying-glass me-2"></i>Public File Search</h1>
            <p>Enter the File Number to check the current status of any registered file.</p>
        </div>
    </section>

    {{-- CONTENT --}}
    <div class="container" style="max-width:720px;padding-bottom:60px;">
        <div class="search-card">

            {{-- Search form --}}
            <form method="GET" action="{{ route('public.file.search.result') }}" novalidate>
                @csrf
                <label class="form-label fw-600 mb-1">File Number</label>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">
                        <i class="fa-solid fa-hashtag text-muted"></i>
                    </span>
                    <input type="text"
                           name="file_number"
                           class="form-control @error('file_number') is-invalid @enderror"
                           placeholder="e.g. HR/FIN/2026/234"
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
                <div class="text-muted" style="font-size:.8rem;">
                    <i class="fa-solid fa-shield-halved me-1 text-primary"></i>
                    Only publicly available information is shown. No internal data is exposed.
                </div>
            </form>

            {{-- File not found --}}
            @if(session('search_error'))
            <div class="not-found-box mt-4">
                <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('search_error') }}
            </div>
            @endif

            {{-- ── RESULT ──────────────────────────────────────────── --}}
            @isset($result)
            <div class="mt-4">

                {{-- Found badge --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-circle-check text-success fs-5"></i>
                    <span class="fw-700" style="font-size:1.05rem;">File Found</span>
                </div>

                {{-- Basic info --}}
                <div class="result-card mb-0">
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
                        <span class="result-label"><i class="fa-solid fa-circle-dot me-2 text-primary"></i>Status</span>
                        <span class="result-value">
                            @php $sk = strtolower(str_replace(' ', '-', $result['status'])); @endphp
                            <span class="status-badge status-{{ $sk }}">{{ $result['status'] }}</span>
                        </span>
                    </div>
                    <div class="result-row">
                        <span class="result-label"><i class="fa-solid fa-calendar me-2 text-primary"></i>Created Date</span>
                        <span class="result-value">{{ $result['created_date'] }}</span>
                    </div>
                </div>

                {{-- ── PUBLIC FILE JOURNEY ─────────────────────────── --}}
                @isset($journey)
                @if(count($journey) > 0)
                <div class="pjt-section">

                    <div class="pjt-title">
                        <i class="fa-solid fa-route" style="color:#2563eb;"></i>
                        File Journey
                        <span style="font-size:.7rem;font-weight:500;color:#64748b;text-transform:none;letter-spacing:0;">
                            — Department-level milestones
                        </span>
                    </div>

                    <div class="pjt-scroll">
                        <div class="pjt-track">
                            @foreach($journey as $idx => $node)

                            {{-- Arrow between nodes --}}
                            @if($idx > 0)
                            <div class="pjt-arrow" aria-hidden="true">
                                <div class="pjt-arrow-line"></div>
                            </div>
                            @endif

                            {{-- Node card --}}
                            <div class="pjt-card {{ $node['is_current'] ? 'pjt-card-current' : '' }}"
                                 style="animation-delay: {{ $idx * 90 }}ms">

                                @if($node['is_current'])
                                <span class="pjt-current-badge">
                                    <i class="fa-solid fa-circle-check fa-xs me-1"></i>Current
                                </span>
                                @endif

                                <div class="pjt-icon">
                                    <i class="fa-solid fa-building-columns fa-sm"></i>
                                </div>

                                <div class="pjt-dept">{{ $node['dept_name'] }}</div>

                                <div class="pjt-action">
                                    @if($node['action'] === 'Created')
                                        <i class="fa-solid fa-file-circle-plus fa-xs me-1"></i>{{ $node['action'] }}
                                    @elseif($node['action'] === 'Current')
                                        <i class="fa-solid fa-circle-dot fa-xs me-1"></i>In Progress
                                    @else
                                        <i class="fa-solid fa-inbox fa-xs me-1"></i>{{ $node['action'] }}
                                    @endif
                                </div>

                                <div class="pjt-date">
                                    <i class="fa-regular fa-calendar fa-xs"></i>
                                    {{ $node['date'] }}
                                </div>
                                <div class="pjt-date">
                                    <i class="fa-regular fa-clock fa-xs"></i>
                                    {{ $node['time'] }}
                                </div>

                                @if($node['remark'])
                                <div class="pjt-remark">
                                    <i class="fa-solid fa-quote-left fa-xs me-1" style="color:#94a3b8;"></i>{{ $node['remark'] }}
                                </div>
                                @endif

                            </div>

                            @endforeach
                        </div>
                    </div>

                    <p class="text-muted mt-3 mb-0" style="font-size:.78rem;">
                        <i class="fa-solid fa-shield-halved me-1 text-primary"></i>
                        Only department-level milestones are shown. Internal movements and employee details are not disclosed.
                    </p>

                </div>
                @endif
                @endisset

                <p class="text-muted mt-3 mb-0" style="font-size:.8rem;">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    For further details, please contact the relevant department or
                    <a href="{{ route('login') }}">login to the portal</a>.
                </p>
            </div>
            @endisset

        </div>
    </div>

    {{-- FOOTER --}}
    <footer style="background:#1e3a5f;color:#94a3b8;text-align:center;padding:1.25rem;font-size:.82rem;">
        &copy; {{ date('Y') }} FileTrack Office Portal &mdash; Government File Tracking System
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
