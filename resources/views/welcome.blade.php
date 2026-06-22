<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Tracking System | Smart Department File Management</title>
    <meta name="description" content="A secure, role-based File Tracking System for organizations to manage, monitor, and transfer files across departments.">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --fts-blue-900: #0b2c5c;
            --fts-blue-700: #15448c;
            --fts-blue-600: #1a56b0;
            --fts-blue-500: #2367d1;
            --fts-blue-100: #e8f0fc;
            --fts-gray-900: #1c2331;
            --fts-gray-700: #4a5468;
            --fts-gray-500: #6b7787;
            --fts-gray-200: #e7eaf0;
            --fts-gray-100: #f5f7fa;
            --fts-white: #ffffff;
            --fts-radius: 14px;
            --fts-shadow-sm: 0 2px 8px rgba(15, 35, 75, 0.06);
            --fts-shadow-md: 0 10px 30px rgba(15, 35, 75, 0.10);
            --fts-shadow-lg: 0 20px 50px rgba(15, 35, 75, 0.16);
            --fts-transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--fts-gray-900);
            background-color: var(--fts-white);
            line-height: 1.7;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        a {
            text-decoration: none;
        }

        .section-padding {
            padding: 100px 0;
        }

        @media (max-width: 768px) {
            .section-padding {
                padding: 64px 0;
            }
        }

        .text-blue-900 {
            color: var(--fts-blue-900) !important;
        }

        .text-blue-600 {
            color: var(--fts-blue-600) !important;
        }

        .text-gray-500 {
            color: var(--fts-gray-500) !important;
        }

        .text-gray-700 {
            color: var(--fts-gray-700) !important;
        }

        .bg-light-soft {
            background-color: var(--fts-gray-100);
        }

        .bg-blue-soft {
            background-color: var(--fts-blue-100);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--fts-blue-600);
            background: var(--fts-blue-100);
            padding: 6px 16px;
            border-radius: 50px;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            color: var(--fts-blue-900);
            margin-bottom: 16px;
        }

        .section-subtitle {
            color: var(--fts-gray-500);
            max-width: 680px;
            margin: 0 auto;
            font-size: 1.05rem;
        }

        /* ===== NAVBAR ===== */
        .navbar-fts {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 rgba(15, 35, 75, 0.06);
            transition: var(--fts-transition);
            padding: 14px 0;
            z-index: 1050;
        }

        .navbar-fts.scrolled {
            box-shadow: var(--fts-shadow-sm);
            padding: 10px 0;
        }

        .navbar-brand-fts {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--fts-blue-900) !important;
        }

        .navbar-brand-fts .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--fts-blue-600), var(--fts-blue-900));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
            box-shadow: var(--fts-shadow-sm);
        }

        .navbar-fts .nav-link {
            font-weight: 500;
            color: var(--fts-gray-700) !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: var(--fts-transition);
            position: relative;
        }

        .navbar-fts .nav-link:hover {
            color: var(--fts-blue-600) !important;
            background: var(--fts-blue-100);
        }

        .btn-fts-primary {
            background: linear-gradient(135deg, var(--fts-blue-600), var(--fts-blue-700));
            color: #fff;
            border: none;
            padding: 10px 26px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: var(--fts-shadow-sm);
            transition: var(--fts-transition);
        }

        .btn-fts-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--fts-shadow-md);
            color: #fff;
        }

        .btn-fts-outline {
            border: 1.5px solid var(--fts-gray-200);
            color: var(--fts-blue-900);
            padding: 10px 26px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--fts-transition);
            background: #fff;
        }

        .btn-fts-outline:hover {
            border-color: var(--fts-blue-600);
            color: var(--fts-blue-600);
            transform: translateY(-2px);
        }

        .btn-fts-light {
            background: rgba(255, 255, 255, 0.15);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            padding: 10px 26px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--fts-transition);
        }

        .btn-fts-light:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ===== HERO ===== */
        .hero-section {
            background: radial-gradient(circle at 15% 20%, rgba(35, 103, 209, 0.18), transparent 45%),
                linear-gradient(135deg, var(--fts-blue-900) 0%, var(--fts-blue-700) 55%, var(--fts-blue-600) 100%);
            padding: 160px 0 120px;
            position: relative;
            overflow: hidden;
            color: #fff;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: -100px;
            right: -100px;
            width: 420px;
            height: 420px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -140px;
            left: -80px;
            width: 320px;
            height: 320px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .hero-section h1 {
            font-size: clamp(2.1rem, 4.2vw, 3.4rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 22px;
        }

        .hero-section .lead {
            font-size: clamp(1rem, 1.4vw, 1.2rem);
            color: rgba(255, 255, 255, 0.85);
            max-width: 540px;
            margin-bottom: 36px;
        }

        .hero-stats-mini {
            display: flex;
            gap: 28px;
            margin-top: 48px;
            flex-wrap: wrap;
        }

        .hero-stats-mini div h3 {
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 0;
            color: #fff;
        }

        .hero-stats-mini div span {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Dashboard illustration (CSS-built) */
        .dashboard-mock {
            background: #fff;
            border-radius: 18px;
            box-shadow: var(--fts-shadow-lg);
            padding: 18px;
            transform: perspective(1200px) rotateY(-6deg) rotateX(2deg);
            transition: transform 0.6s ease;
        }

        .dashboard-mock:hover {
            transform: perspective(1200px) rotateY(0deg) rotateX(0deg);
        }

        .dashboard-mock .mock-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--fts-gray-200);
            margin-bottom: 14px;
        }

        .mock-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .mock-card {
            background: var(--fts-gray-100);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
        }

        .mock-card .mock-bar {
            height: 8px;
            border-radius: 5px;
            background: var(--fts-gray-200);
            margin-top: 8px;
        }

        .mock-card .mock-bar.fill {
            background: linear-gradient(90deg, var(--fts-blue-600), var(--fts-blue-500));
            width: 70%;
        }

        .mock-row {
            display: flex;
            gap: 10px;
        }

        .mock-stat {
            flex: 1;
            background: var(--fts-blue-100);
            border-radius: 10px;
            padding: 12px;
            text-align: center;
        }

        .mock-stat strong {
            display: block;
            color: var(--fts-blue-900);
            font-size: 1.1rem;
        }

        .mock-stat span {
            font-size: 0.7rem;
            color: var(--fts-gray-500);
        }

        .floating-chip {
            position: absolute;
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--fts-shadow-md);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--fts-gray-900);
            animation: floatChip 4s ease-in-out infinite;
        }

        .floating-chip.chip-1 {
            top: 8%;
            right: -6%;
            animation-delay: 0s;
        }

        .floating-chip.chip-2 {
            bottom: 10%;
            left: -8%;
            animation-delay: 1.2s;
        }

        @keyframes floatChip {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* ===== FADE-IN ON SCROLL ===== */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== FEATURE CARDS ===== */
        .feature-card {
            background: #fff;
            border: 1px solid var(--fts-gray-200);
            border-radius: var(--fts-radius);
            padding: 32px 26px;
            height: 100%;
            transition: var(--fts-transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, var(--fts-blue-600), var(--fts-blue-900));
            transition: height 0.35s ease;
        }

        .feature-card:hover {
            box-shadow: var(--fts-shadow-md);
            transform: translateY(-6px);
            border-color: transparent;
        }

        .feature-card:hover::before {
            height: 100%;
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--fts-blue-100);
            color: var(--fts-blue-600);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            transition: var(--fts-transition);
        }

        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, var(--fts-blue-600), var(--fts-blue-900));
            color: #fff;
            transform: rotate(-6deg) scale(1.05);
        }

        .feature-card h5 {
            color: var(--fts-blue-900);
            margin-bottom: 10px;
            font-size: 1.08rem;
        }

        .feature-card p {
            color: var(--fts-gray-500);
            font-size: 0.92rem;
            margin-bottom: 0;
        }

        /* ===== WORKFLOW ===== */
        .workflow-wrap {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0;
        }

        .workflow-step {
            background: #fff;
            border: 1px solid var(--fts-gray-200);
            border-radius: var(--fts-radius);
            padding: 22px 18px;
            text-align: center;
            width: 175px;
            box-shadow: var(--fts-shadow-sm);
            transition: var(--fts-transition);
        }

        .workflow-step:hover {
            box-shadow: var(--fts-shadow-md);
            transform: translateY(-5px);
            border-color: var(--fts-blue-500);
        }

        .workflow-step .wf-num {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--fts-blue-600), var(--fts-blue-900));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0 auto 12px;
        }

        .workflow-step i {
            font-size: 1.6rem;
            color: var(--fts-blue-600);
            margin-bottom: 8px;
            display: block;
        }

        .workflow-step h6 {
            color: var(--fts-blue-900);
            font-size: 0.88rem;
            margin-bottom: 0;
            font-weight: 700;
        }

        .workflow-arrow {
            color: var(--fts-blue-500);
            font-size: 1.4rem;
            margin: 0 6px;
        }

        @media (max-width: 768px) {
            .workflow-arrow {
                transform: rotate(90deg);
                margin: 6px 0;
            }
        }

        /* ===== ROLE CARDS ===== */
        .role-card {
            border-radius: var(--fts-radius);
            padding: 36px 30px;
            height: 100%;
            transition: var(--fts-transition);
            position: relative;
            color: #fff;
            overflow: hidden;
        }

        .role-card.role-super {
            background: linear-gradient(160deg, var(--fts-blue-900), #081b3d);
        }

        .role-card.role-admin {
            background: linear-gradient(160deg, var(--fts-blue-700), var(--fts-blue-600));
        }

        .role-card.role-user {
            background: linear-gradient(160deg, #2c4a78, #1d3556);
        }

        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--fts-shadow-lg);
        }

        .role-card .role-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .role-card h4 {
            color: #fff;
            margin-bottom: 6px;
        }

        .role-card .role-tag {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(255, 255, 255, 0.65);
            margin-bottom: 20px;
            display: block;
        }

        .role-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .role-card ul li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .role-card ul li i {
            color: #8fd0ff;
            margin-top: 3px;
        }

        /* ===== WHY CHOOSE ===== */
        .why-item {
            display: flex;
            gap: 18px;
            padding: 22px;
            border-radius: var(--fts-radius);
            transition: var(--fts-transition);
            height: 100%;
            background: #fff;
            border: 1px solid var(--fts-gray-200);
        }

        .why-item:hover {
            box-shadow: var(--fts-shadow-md);
            background: var(--fts-blue-100);
            border-color: transparent;
        }

        .why-icon {
            min-width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--fts-blue-900);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .why-item h6 {
            color: var(--fts-blue-900);
            margin-bottom: 6px;
        }

        .why-item p {
            color: var(--fts-gray-500);
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* ===== STATS ===== */
        .stats-section {
            background: linear-gradient(135deg, var(--fts-blue-900), var(--fts-blue-700));
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-box {
            text-align: center;
            padding: 20px 10px;
        }

        .stat-box .stat-number {
            font-size: clamp(2.2rem, 4vw, 3rem);
            font-weight: 800;
            display: block;
            margin-bottom: 6px;
        }

        .stat-box .stat-label {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
        }

        /* ===== ABOUT ===== */
        .about-visual {
            background: var(--fts-blue-100);
            border-radius: 20px;
            padding: 40px;
            position: relative;
        }

        .about-visual .about-icon-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .about-icon-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            text-align: center;
            box-shadow: var(--fts-shadow-sm);
            transition: var(--fts-transition);
        }

        .about-icon-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--fts-shadow-md);
        }

        .about-icon-card i {
            font-size: 1.7rem;
            color: var(--fts-blue-600);
            margin-bottom: 10px;
            display: block;
        }

        .about-icon-card span {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--fts-gray-700);
        }

        /* ===== FUTURE ENHANCEMENTS ===== */
        .future-card {
            background: #fff;
            border: 1px dashed var(--fts-gray-200);
            border-radius: var(--fts-radius);
            padding: 28px 22px;
            text-align: center;
            height: 100%;
            transition: var(--fts-transition);
        }

        .future-card:hover {
            border-style: solid;
            border-color: var(--fts-blue-500);
            box-shadow: var(--fts-shadow-md);
            transform: translateY(-5px);
        }

        .future-card .future-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--fts-blue-100);
            color: var(--fts-blue-600);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 1.3rem;
        }

        .future-card h6 {
            color: var(--fts-blue-900);
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .future-card span.badge-soon {
            font-size: 0.7rem;
            color: var(--fts-blue-600);
            background: var(--fts-blue-100);
            padding: 3px 10px;
            border-radius: 50px;
            font-weight: 600;
        }

        /* ===== CTA BANNER ===== */
        .cta-banner {
            background: linear-gradient(135deg, var(--fts-blue-600), var(--fts-blue-900));
            border-radius: 24px;
            padding: 60px 40px;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            top: -120px;
            left: -80px;
        }

        /* ===== FOOTER ===== */
        .footer-fts {
            background: var(--fts-gray-900);
            color: rgba(255, 255, 255, 0.7);
            padding-top: 70px;
        }

        .footer-fts h6 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-fts ul {
            list-style: none;
            padding: 0;
        }

        .footer-fts ul li {
            margin-bottom: 12px;
        }

        .footer-fts ul li a {
            color: rgba(255, 255, 255, 0.6);
            transition: var(--fts-transition);
            font-size: 0.92rem;
        }

        .footer-fts ul li a:hover {
            color: #fff;
            padding-left: 4px;
        }

        .footer-fts .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 16px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 50px;
            padding: 22px 0;
            font-size: 0.85rem;
        }

        .social-icon-fts {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: var(--fts-transition);
        }

        .social-icon-fts:hover {
            background: var(--fts-blue-600);
            transform: translateY(-3px);
        }

        /* Scroll to top */
        .scroll-top-btn {
            position: fixed;
            bottom: 26px;
            right: 26px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--fts-blue-600);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--fts-shadow-md);
            opacity: 0;
            visibility: hidden;
            transition: var(--fts-transition);
            z-index: 999;
            border: none;
            font-size: 1.1rem;
        }

        .scroll-top-btn.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top-btn:hover {
            background: var(--fts-blue-900);
            transform: translateY(-3px);
        }

        .divider-soft {
            height: 1px;
            background: var(--fts-gray-200);
        }
    </style>
</head>

<body>

    <!-- ===================== NAVBAR ===================== -->
    <nav class="navbar navbar-expand-lg navbar-fts fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand-fts" href="#home">
                <span class="brand-icon"><i class="bi bi-folder2-open"></i></span>
                File Tracking System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mt-3 mt-lg-0 gap-1">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#workflow">Workflow</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roles">User Roles</a></li>
                    <li class="nav-item"><a class="nav-link" href="#why">Why Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
                <div class="d-flex gap-2 mt-3 mt-lg-0">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}" class="btn-fts-primary">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn-fts-outline">Login</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-fts-primary">Get Started</a>
                    @endif
                    @endauth
                    @else
                    <a href="#" class="btn-fts-outline">Login</a>
                    <a href="#" class="btn-fts-primary">Get Started</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ===================== HERO SECTION ===================== -->
    <header class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-shield-check"></i> Trusted by Government &amp; Enterprise Teams
                    </div>
                    <h1>Smart File Tracking &amp; Department Management System</h1>
                    <p class="lead">Track, transfer, monitor, and manage organizational files securely across departments — with full role-based access control and a complete audit trail.</p>
                    <div class="d-flex flex-wrap gap-3">
                        @if (Route::has('login'))
                        @auth
                        <a href="{{ url('/dashboard') }}" class="btn-fts-light"><i class="bi bi-speedometer2 me-2"></i>Go to Dashboard</a>
                        @else
                        <a href="{{ route('login') }}" class="btn-fts-light"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-fts-primary"><i class="bi bi-rocket-takeoff me-2"></i>Get Started</a>
                        @endif
                        @endauth
                        @else
                        <a href="#" class="btn-fts-light"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                        <a href="#" class="btn-fts-primary"><i class="bi bi-rocket-takeoff me-2"></i>Get Started</a>
                        @endif
                    </div>

                    <div class="hero-stats-mini">
                        <div>
                            <h3>100%</h3>
                            <span>File Traceability</span>
                        </div>
                        <div>
                            <h3>24/7</h3>
                            <span>Real-Time Monitoring</span>
                        </div>
                        <div>
                            <h3>Multi</h3>
                            <span>Department Support</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative px-md-4">
                        <div class="dashboard-mock">
                            <div class="mock-header">
                                <span class="mock-dot" style="background:#ff5f57;"></span>
                                <span class="mock-dot" style="background:#febc2e;"></span>
                                <span class="mock-dot" style="background:#28c840;"></span>
                                <span class="ms-2 text-gray-500" style="font-size:0.75rem;">File Tracking Dashboard</span>
                            </div>
                            <div class="mock-row mb-3">
                                <div class="mock-stat">
                                    <strong>128</strong>
                                    <span>Active Files</span>
                                </div>
                                <div class="mock-stat">
                                    <strong>34</strong>
                                    <span>In Transfer</span>
                                </div>
                                <div class="mock-stat">
                                    <strong>09</strong>
                                    <span>Departments</span>
                                </div>
                            </div>
                            <div class="mock-card">
                                <div class="d-flex justify-content-between">
                                    <span style="font-size:0.78rem; font-weight:600; color:#1c2331;">File #FTS-2026-0451</span>
                                    <span class="badge text-bg-success" style="font-size:0.65rem;">Approved</span>
                                </div>
                                <div class="mock-bar fill"></div>
                            </div>
                            <div class="mock-card">
                                <div class="d-flex justify-content-between">
                                    <span style="font-size:0.78rem; font-weight:600; color:#1c2331;">File #FTS-2026-0452</span>
                                    <span class="badge text-bg-warning" style="font-size:0.65rem;">Pending</span>
                                </div>
                                <div class="mock-bar" style="width:45%; background:linear-gradient(90deg,#2367d1,#1a56b0);"></div>
                            </div>
                            <div class="mock-card mb-0">
                                <div class="d-flex justify-content-between">
                                    <span style="font-size:0.78rem; font-weight:600; color:#1c2331;">File #FTS-2026-0453</span>
                                    <span class="badge text-bg-primary" style="font-size:0.65rem;">In Review</span>
                                </div>
                                <div class="mock-bar" style="width:30%; background:linear-gradient(90deg,#2367d1,#1a56b0);"></div>
                            </div>
                        </div>
                        <div class="floating-chip chip-1 d-none d-md-flex">
                            <i class="bi bi-check-circle-fill text-success"></i> Transfer Approved
                        </div>
                        <div class="floating-chip chip-2 d-none d-md-flex">
                            <i class="bi bi-shield-lock-fill text-primary"></i> Role Secured
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @if (Route::has('login'))
    <div class="d-none"></div>
    @endif

    <!-- ===================== FEATURES SECTION ===================== -->
    <section class="section-padding" id="features">
        <div class="container">
            <div class="text-center mb-5 fade-in-up">
                <span class="eyebrow"><i class="bi bi-stars"></i> Core Features</span>
                <h2 class="section-title">Everything Your Organization Needs</h2>
                <p class="section-subtitle mx-auto">A complete toolkit to digitize, secure, and streamline file movement across every department.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
                        <h5>Role-Based Access Control</h5>
                        <p>Granular permissions ensure every user sees only what they're authorized to access.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-building"></i></div>
                        <h5>Department Management</h5>
                        <p>Organize and structure your institution into departments with dedicated workflows.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-people"></i></div>
                        <h5>User Management</h5>
                        <p>Create, assign, and manage users with designations across the organization.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-file-earmark-plus"></i></div>
                        <h5>File Creation &amp; Tracking</h5>
                        <p>Create digital files instantly and track their complete lifecycle from start to end.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-send"></i></div>
                        <h5>File Transfer Workflow</h5>
                        <p>Seamlessly transfer files between departments with structured approval steps.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-diagram-3"></i></div>
                        <h5>Cross Department Approval</h5>
                        <p>Multi-level approval chains keep every transfer accountable and verified.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                        <h5>File Timeline History</h5>
                        <p>View a complete chronological history of every action taken on a file.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-search"></i></div>
                        <h5>Search &amp; Filtering</h5>
                        <p>Locate any file instantly with powerful search and multi-criteria filters.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-fingerprint"></i></div>
                        <h5>Secure Authentication</h5>
                        <p>Industry-standard authentication protects every login and session.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-activity"></i></div>
                        <h5>Real-Time Monitoring</h5>
                        <p>Live dashboards keep administrators informed of file status at all times.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== WORKFLOW SECTION ===================== -->
    <section class="section-padding bg-light-soft" id="workflow">
        <div class="container">
            <div class="text-center mb-5 fade-in-up">
                <span class="eyebrow"><i class="bi bi-signpost-split"></i> Process Flow</span>
                <h2 class="section-title">A Transparent File Workflow</h2>
                <p class="section-subtitle mx-auto">Every file follows a clear, auditable path from creation to closure.</p>
            </div>

            <div class="workflow-wrap fade-in-up">
                <div class="workflow-step">
                    <span class="wf-num">1</span>
                    <i class="bi bi-file-earmark-plus"></i>
                    <h6>Create File</h6>
                </div>
                <i class="bi bi-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="wf-num">2</span>
                    <i class="bi bi-building"></i>
                    <h6>Assign Department</h6>
                </div>
                <i class="bi bi-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="wf-num">3</span>
                    <i class="bi bi-send"></i>
                    <h6>Transfer File</h6>
                </div>
                <i class="bi bi-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="wf-num">4</span>
                    <i class="bi bi-patch-check"></i>
                    <h6>Admin Approval</h6>
                </div>
                <i class="bi bi-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="wf-num">5</span>
                    <i class="bi bi-clock-history"></i>
                    <h6>Track Timeline</h6>
                </div>
                <i class="bi bi-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="wf-num">6</span>
                    <i class="bi bi-journal-check"></i>
                    <h6>Complete Audit Trail</h6>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== USER ROLES SECTION ===================== -->
    <section class="section-padding" id="roles">
        <div class="container">
            <div class="text-center mb-5 fade-in-up">
                <span class="eyebrow"><i class="bi bi-person-badge"></i> Access Hierarchy</span>
                <h2 class="section-title">Built for Every Level of Your Organization</h2>
                <p class="section-subtitle mx-auto">Each role is scoped with precise permissions to maintain security and accountability.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 fade-in-up">
                    <div class="role-card role-super">
                        <div class="role-icon"><i class="bi bi-person-gear"></i></div>
                        <span class="role-tag">Highest Authority</span>
                        <h4>Super Admin</h4>
                        <ul class="mt-3">
                            <li><i class="bi bi-check-circle-fill"></i> Manage Departments</li>
                            <li><i class="bi bi-check-circle-fill"></i> Manage Designations</li>
                            <li><i class="bi bi-check-circle-fill"></i> Create Admins</li>
                            <li><i class="bi bi-check-circle-fill"></i> View All Files</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 fade-in-up">
                    <div class="role-card role-admin">
                        <div class="role-icon"><i class="bi bi-person-vcard"></i></div>
                        <span class="role-tag">Department Authority</span>
                        <h4>Admin</h4>
                        <ul class="mt-3">
                            <li><i class="bi bi-check-circle-fill"></i> Manage Department Users</li>
                            <li><i class="bi bi-check-circle-fill"></i> Approve Transfers</li>
                            <li><i class="bi bi-check-circle-fill"></i> View Department Files</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 fade-in-up">
                    <div class="role-card role-user">
                        <div class="role-icon"><i class="bi bi-person"></i></div>
                        <span class="role-tag">Standard Access</span>
                        <h4>User</h4>
                        <ul class="mt-3">
                            <li><i class="bi bi-check-circle-fill"></i> Create Files</li>
                            <li><i class="bi bi-check-circle-fill"></i> Transfer Files</li>
                            <li><i class="bi bi-check-circle-fill"></i> Track File Status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== WHY CHOOSE SECTION ===================== -->
    <section class="section-padding bg-light-soft" id="why">
        <div class="container">
            <div class="text-center mb-5 fade-in-up">
                <span class="eyebrow"><i class="bi bi-award"></i> Our Advantage</span>
                <h2 class="section-title">Why Choose Our System</h2>
                <p class="section-subtitle mx-auto">Designed to bring clarity, speed, and accountability to organizational file handling.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="why-item">
                        <div class="why-icon"><i class="bi bi-eye"></i></div>
                        <div>
                            <h6>Complete Transparency</h6>
                            <p>Every action on a file is visible and traceable in real time.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="why-item">
                        <div class="why-icon"><i class="bi bi-lightning-charge"></i></div>
                        <div>
                            <h6>Faster File Processing</h6>
                            <p>Automated routing cuts down manual delays and bottlenecks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="why-item">
                        <div class="why-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <h6>Secure Access Control</h6>
                            <p>Role-based permissions keep sensitive files protected.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="why-item">
                        <div class="why-icon"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <h6>Digital Audit Trail</h6>
                            <p>Every transfer and approval is logged for future reference.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="why-item">
                        <div class="why-icon"><i class="bi bi-recycle"></i></div>
                        <div>
                            <h6>Reduced Paperwork</h6>
                            <p>Go fully digital and minimize dependency on physical files.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="why-item">
                        <div class="why-icon"><i class="bi bi-bar-chart-line"></i></div>
                        <div>
                            <h6>Improved Accountability</h6>
                            <p>Clear ownership at every stage keeps teams responsible.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== STATISTICS SECTION ===================== -->
    <section class="section-padding stats-section" id="stats">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-6 fade-in-up">
                    <div class="stat-box">
                        <span class="stat-number" data-count="100" data-suffix="%">0%</span>
                        <span class="stat-label">File Traceability</span>
                    </div>
                </div>
                <div class="col-lg-3 col-6 fade-in-up">
                    <div class="stat-box">
                        <span class="stat-number" data-count="100" data-suffix="%">0%</span>
                        <span class="stat-label">Secure Role Management</span>
                    </div>
                </div>
                <div class="col-lg-3 col-6 fade-in-up">
                    <div class="stat-box">
                        <span class="stat-number" data-count="100" data-suffix="%">0%</span>
                        <span class="stat-label">Department Wise Monitoring</span>
                    </div>
                </div>
                <div class="col-lg-3 col-6 fade-in-up">
                    <div class="stat-box">
                        <span class="stat-number" data-count="100" data-suffix="%">0%</span>
                        <span class="stat-label">Complete Transfer History</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== ABOUT SECTION ===================== -->
    <section class="section-padding" id="about">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6 fade-in-up">
                    <span class="eyebrow"><i class="bi bi-info-circle"></i> About The System</span>
                    <h2 class="section-title">Digitizing File Movement, End to End</h2>
                    <p class="text-gray-700">The File Tracking System helps organizations move away from manual, paper-based processes by digitizing how files are created, assigned, transferred, and closed. It gives administrators full visibility into file ownership at every stage, ensuring nothing is lost or delayed between departments.</p>
                    <p class="text-gray-700">By maintaining a structured audit trail and enforcing role-based responsibilities, the system strengthens accountability and streamlines inter-department communication — making it suitable for government offices, universities, companies, and large enterprises alike.</p>
                    <a href="#contact" class="btn-fts-primary mt-2 d-inline-block">Talk to Us <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="col-lg-6 fade-in-up">
                    <div class="about-visual">
                        <div class="about-icon-grid">
                            <div class="about-icon-card">
                                <i class="bi bi-cloud-check"></i>
                                <span>Digital File Movement</span>
                            </div>
                            <div class="about-icon-card">
                                <i class="bi bi-person-check"></i>
                                <span>Ownership Monitoring</span>
                            </div>
                            <div class="about-icon-card">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Accountability</span>
                            </div>
                            <div class="about-icon-card">
                                <i class="bi bi-chat-square-dots"></i>
                                <span>Inter-Dept. Communication</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FUTURE ENHANCEMENTS ===================== -->
    <section class="section-padding bg-light-soft" id="future">
        <div class="container">
            <div class="text-center mb-5 fade-in-up">
                <span class="eyebrow"><i class="bi bi-rocket"></i> Roadmap</span>
                <h2 class="section-title">Future Enhancements</h2>
                <p class="section-subtitle mx-auto">We're continually evolving the platform to serve organizations even better.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-2 col-md-4 col-6 fade-in-up">
                    <div class="future-card">
                        <div class="future-icon"><i class="bi bi-phone"></i></div>
                        <h6>Mobile Application</h6>
                        <span class="badge-soon">Upcoming</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 fade-in-up">
                    <div class="future-card">
                        <div class="future-icon"><i class="bi bi-envelope-check"></i></div>
                        <h6>Email Notifications</h6>
                        <span class="badge-soon">Upcoming</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 fade-in-up">
                    <div class="future-card">
                        <div class="future-icon"><i class="bi bi-chat-dots"></i></div>
                        <h6>SMS Alerts</h6>
                        <span class="badge-soon">Upcoming</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 fade-in-up">
                    <div class="future-card">
                        <div class="future-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <h6>Analytics Dashboard</h6>
                        <span class="badge-soon">Upcoming</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 fade-in-up">
                    <div class="future-card">
                        <div class="future-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <h6>Advanced Reporting</h6>
                        <span class="badge-soon">Upcoming</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 fade-in-up">
                    <div class="future-card">
                        <div class="future-icon"><i class="bi bi-layers"></i></div>
                        <h6>File Version Control</h6>
                        <span class="badge-soon">Upcoming</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== CTA BANNER ===================== -->
    <section class="section-padding" id="cta">
        <div class="container">
            <div class="cta-banner fade-in-up">
                <h2 class="mb-3" style="color:#fff;">Ready to Modernize Your File Management?</h2>
                <p class="mb-4" style="color:rgba(255,255,255,0.85); max-width:600px; margin-inline:auto;">Join organizations that have transformed their file handling with secure, transparent, and efficient digital workflows.</p>
                <div class="d-flex justify-content-center flex-wrap gap-3">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}" class="btn-fts-light"><i class="bi bi-speedometer2 me-2"></i>Go to Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="btn-fts-light"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-fts-primary"><i class="bi bi-rocket-takeoff me-2"></i>Get Started Now</a>
                    @endif
                    @endauth
                    @else
                    <a href="#" class="btn-fts-light"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                    <a href="#" class="btn-fts-primary"><i class="bi bi-rocket-takeoff me-2"></i>Get Started Now</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FOOTER ===================== -->
    <footer class="footer-fts" id="contact">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <span class="brand-icon"><i class="bi bi-folder2-open"></i></span>
                        File Tracking System
                    </div>
                    <p style="font-size:0.92rem; max-width:320px;">A secure, role-based platform for organizations to manage, monitor, and track files across departments with full accountability.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="social-icon-fts"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon-fts"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-icon-fts"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="social-icon-fts"><i class="bi bi-github"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6>Quick Links</h6>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#workflow">Workflow</a></li>
                        <li><a href="#roles">User Roles</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6>Features</h6>
                    <ul>
                        <li><a href="#features">Role-Based Access</a></li>
                        <li><a href="#features">File Transfer Workflow</a></li>
                        <li><a href="#features">Audit Trail</a></li>
                        <li><a href="#features">Real-Time Monitoring</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6>Contact</h6>
                    <ul>
                        <li><i class="bi bi-geo-alt me-2"></i>Your Organization Address, City</li>
                        <li><i class="bi bi-envelope me-2"></i>support@filetrackingsystem.com</li>
                        <li><i class="bi bi-telephone me-2"></i>+91 00000 00000</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span>&copy; <span id="currentYear"></span> File Tracking System. All Rights Reserved.</span>
                <span>Built with Laravel, MySQL &amp; Bootstrap 5</span>
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <button class="scroll-top-btn" id="scrollTopBtn" aria-label="Scroll to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== Footer current year =====
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        // ===== Navbar scroll effect =====
        const navbar = document.getElementById('mainNavbar');
        const scrollTopBtn = document.getElementById('scrollTopBtn');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            if (window.scrollY > 400) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // ===== Collapse mobile navbar after link click =====
        document.querySelectorAll('#navbarMain .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                const navbarCollapse = document.getElementById('navbarMain');
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            });
        });

        // ===== Fade-in on scroll (Intersection Observer) =====
        const fadeEls = document.querySelectorAll('.fade-in-up');
        const fadeObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    fadeObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15
        });

        fadeEls.forEach(function(el) {
            fadeObserver.observe(el);
        });

        // ===== Animated counters for statistics =====
        const counters = document.querySelectorAll('.stat-number');
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.4
        });

        counters.forEach(function(el) {
            counterObserver.observe(el);
        });

        function animateCounter(el) {
            const target = parseInt(el.getAttribute('data-count'), 10);
            const suffix = el.getAttribute('data-suffix') || '';
            const duration = 1400;
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
                const value = Math.floor(eased * target);
                el.textContent = value + suffix;

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    el.textContent = target + suffix;
                }
            }
            requestAnimationFrame(update);
        }
    </script>
</body>

</html>