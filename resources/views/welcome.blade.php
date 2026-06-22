<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Government-grade file tracking and department management system.">
    <title>FileTrack Office Portal &mdash; Government File Tracking System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
</head>

<body>
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="#home">
                    <span class="brand-icon"><i class="fa-solid fa-folder-tree"></i></span>
                    <span>FileTrack Office</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-lg-center gap-lg-3">
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                        <li class="nav-item"><a class="nav-link" href="#workflow">Workflow</a></li>
                        <li class="nav-item"><a class="nav-link" href="#stats">Statistics</a></li>
                        <li class="nav-item"><a class="nav-link" href="#upload">Public Upload</a></li>
                        <li class="nav-item ms-lg-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-section" id="home">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <span class="eyebrow">Government File Tracking</span>
                        <h1>Track Every File.<br><span>Monitor Every Movement.</span></h1>
                        <p class="hero-text">A secure, transparent, and efficient system for managing official records, departmental transfers, approvals, and document accountability.</p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="#upload" class="btn btn-primary btn-lg"> <i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload File</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg"> <i class="fa-solid fa-right-to-bracket me-2"></i> Login</a>
                        </div>
                        <div class="hero-highlights mt-4">
                            <span><i class="fa-solid fa-circle-check"></i> Department-wise records</span>
                            <span><i class="fa-solid fa-circle-check"></i> Real-time tracking</span>
                            <span><i class="fa-solid fa-circle-check"></i> Audit-ready workflow</span>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="hero-card">
                            <div class="hero-card-head">
                                <span>Today’s Overview</span>
                                <span class="badge bg-success-subtle text-success">Live</span>
                            </div>
                            <div class="hero-card-body">
                                <div class="mini-stat">
                                    <small>Pending Transfers</small>
                                    <h3>{{ $stats['transfers'] }}</h3>
                                </div>
                                <div class="mini-stat">
                                    <small>Active Files</small>
                                    <h3>{{ $stats['files'] }}</h3>
                                </div>
                                <div class="mini-stat">
                                    <small>Registered Users</small>
                                    <h3>{{ $stats['users'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding" id="about">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="eyebrow">About the System</span>
                        <h2 class="section-title">Designed for smooth office operations</h2>
                        <p class="section-text">The platform enables departments to manage incoming and outgoing files, track movement history, route approvals, and ensure accountability across every workflow stage.</p>
                        <ul class="feature-list">
                            <li><i class="fa-solid fa-square-check"></i> Department Management</li>
                            <li><i class="fa-solid fa-square-check"></i> User Management</li>
                            <li><i class="fa-solid fa-square-check"></i> File Tracking</li>
                            <li><i class="fa-solid fa-square-check"></i> Transfer Management</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="info-panel">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="info-box"><i class="fa-solid fa-building-columns"></i><span>Departments</span></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-box"><i class="fa-solid fa-users"></i><span>Users</span></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-box"><i class="fa-solid fa-file-lines"></i><span>Files</span></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-box"><i class="fa-solid fa-right-left"></i><span>Transfers</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-soft" id="features">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="eyebrow">System Features</span>
                    <h2 class="section-title">Everything needed for file lifecycle control</h2>
                </div>
                <div class="row g-4">
                    @php($featureCards = [
                    ['icon' => 'fa-file-circle-plus', 'title' => 'File Management', 'text' => 'Create, edit, review, and archive official documents with clear ownership.'],
                    ['icon' => 'fa-building-user', 'title' => 'Department Management', 'text' => 'Organize team structure, departments, and office responsibilities with ease.'],
                    ['icon' => 'fa-user-gear', 'title' => 'User Management', 'text' => 'Assign roles, maintain records, and control access levels securely.'],
                    ['icon' => 'fa-check-to-slot', 'title' => 'Transfer Approval', 'text' => 'Approve or reject transfer requests with full audit history.'],
                    ['icon' => 'fa-timeline', 'title' => 'Timeline Tracking', 'text' => 'See every event in the file lifecycle from creation to delivery.'],
                    ['icon' => 'fa-magnifying-glass', 'title' => 'Search & Filters', 'text' => 'Locate records quickly with advanced search, status, and date filters.']
                    ])
                    @foreach($featureCards as $card)
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card h-100">
                            <div class="feature-icon"><i class="fa-solid {{ $card['icon'] }}"></i></div>
                            <h5>{{ $card['title'] }}</h5>
                            <p>{{ $card['text'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-padding" id="workflow">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="eyebrow">Workflow</span>
                    <h2 class="section-title">How the process works</h2>
                </div>
                <div class="workflow-row">
                    <div class="workflow-step">
                        <span>1</span>
                        <h6>Create File</h6>
                    </div>
                    <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                    <div class="workflow-step">
                        <span>2</span>
                        <h6>Transfer File</h6>
                    </div>
                    <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                    <div class="workflow-step">
                        <span>3</span>
                        <h6>Approval Request</h6>
                    </div>
                    <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                    <div class="workflow-step">
                        <span>4</span>
                        <h6>Admin Approval</h6>
                    </div>
                    <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                    <div class="workflow-step">
                        <span>5</span>
                        <h6>Timeline Updated</h6>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-soft" id="roles">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="eyebrow">Role Based Access</span>
                    <h2 class="section-title">Designed for every office role</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="role-card">
                            <h5>Super Admin</h5>
                            <p>Full system oversight, department control, and advanced administrative access.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="role-card">
                            <h5>Admin</h5>
                            <p>Handles transfers, approvals, user coordination, and file monitoring.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="role-card">
                            <h5>User</h5>
                            <p>Creates, reviews, and transfers files within assigned departments.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding" id="timeline-showcase">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="eyebrow">Timeline Showcase</span>
                    <h2 class="section-title">Sample file movement history</h2>
                </div>
                <div class="timeline-panel">
                    <div class="timeline-item">
                        <span class="badge badge-created">Created</span>
                        <p>File #FILE-1001 received and registered in the system.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="badge badge-requested">Requested</span>
                        <p>Transfer request sent to the concerned department.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="badge badge-approved">Approved</span>
                        <p>Admin approved the transfer and updated the workflow.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="badge badge-transferred">Transferred</span>
                        <p>Document moved to the next department for action.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="badge badge-delivered">Delivered</span>
                        <p>Final delivery completed and acknowledgement recorded.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-soft" id="upload">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <span class="eyebrow">Public File Submission</span>
                        <h2 class="section-title">Submit a file without logging in</h2>
                        <p class="section-text">Applicants and visitors can submit documents directly for review. Uploaded files are stored securely for later admin access.</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="upload-card">
                            @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <form action="{{ route('public-files.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" name="applicant_name" value="{{ old('applicant_name') }}" class="form-control" placeholder="Applicant Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="form-control" placeholder="Contact Number" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" placeholder="Subject" required>
                                    </div>
                                    <div class="col-12">
                                        <input type="file" name="attachment" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <textarea name="remarks" class="form-control" rows="4" placeholder="Remarks">{{ old('remarks') }}</textarea>
                                    </div>
                                    <div class="col-12"><button type="submit" class="btn btn-primary w-100">Submit File</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding" id="stats">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="eyebrow">Statistics</span>
                    <h2 class="section-title">Live system overview</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="stat-card">
                            <div><small>Total Departments</small>
                                <h3>{{ $stats['departments'] }}</h3>
                            </div>
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="stat-card">
                            <div><small>Total Users</small>
                                <h3>{{ $stats['users'] }}</h3>
                            </div>
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="stat-card">
                            <div><small>Total Files</small>
                                <h3>{{ $stats['files'] }}</h3>
                            </div>
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="stat-card">
                            <div><small>Total Transfers</small>
                                <h3>{{ $stats['transfers'] }}</h3>
                            </div>
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-dark-section">
            <div class="container text-center">
                <h2 class="section-title text-white">Ready to manage your records securely?</h2>
                <p class="text-white-50 mb-4">Access the complete dashboard to handle files, approvals, and departmental workflows.</p>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login to Continue</a>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>