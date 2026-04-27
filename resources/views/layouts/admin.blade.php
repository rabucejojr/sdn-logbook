<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — DOST SDN Logbook</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --dost-blue:              #003a8c;
            --dost-blue-light:        #1565c0;
            --sidebar-width:          260px;
            --sidebar-collapsed-width: 64px;
            --sidebar-transition:     0.25s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            color: #1a202c;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--dost-blue) 0%, #00245a 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width var(--sidebar-transition), transform var(--sidebar-transition);
        }

        /* ── Sidebar brand ── */
        .sidebar-brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text .brand-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            white-space: nowrap;
        }

        .sidebar-brand .brand-text .brand-sub {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.6);
            white-space: nowrap;
        }

        /* Desktop collapse toggle button */
        .sidebar-collapse-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 6px;
            color: rgba(255,255,255,0.7);
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.15s, color 0.15s;
        }
        .sidebar-collapse-btn:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar-collapse-btn i { font-size: 0.85rem; transition: transform var(--sidebar-transition); }

        /* ── Sidebar nav ── */
        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav .nav-label {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.4);
            padding: 0.5rem 1.25rem 0.25rem;
            white-space: nowrap;
            transition: opacity var(--sidebar-transition);
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1.25rem;
            color: rgba(255,255,255,0.75);
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 0;
            transition: background 0.15s, color 0.15s, padding 0.25s, justify-content 0.25s;
            border-left: 3px solid transparent;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #63b3ed;
        }

        .sidebar-nav .nav-link i {
            font-size: 1rem;
            width: 1.2rem;
            flex-shrink: 0;
        }

        .nav-link-text {
            transition: opacity var(--sidebar-transition);
            overflow: hidden;
        }

        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            border-radius: 999px;
            font-size: 0.68rem;
            min-width: 20px;
            padding: 1px 5px;
            text-align: center;
            flex-shrink: 0;
            transition: opacity var(--sidebar-transition);
        }

        /* ── Sidebar footer ── */
        .sidebar-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            overflow: hidden;
            transition: padding var(--sidebar-transition);
        }

        .sidebar-footer .footer-text {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.45);
            white-space: nowrap;
        }

        .sidebar-footer .footer-name {
            color: rgba(255,255,255,0.75);
            font-weight: 600;
        }

        /* ── Desktop collapsed state ── */
        body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed-width); }

        body.sidebar-collapsed .brand-text,
        body.sidebar-collapsed .nav-link-text,
        body.sidebar-collapsed .nav-badge,
        body.sidebar-collapsed .nav-label,
        body.sidebar-collapsed .footer-text {
            display: none;
        }

        body.sidebar-collapsed .sidebar-collapse-btn i {
            transform: rotate(180deg);
        }

        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding: 1rem 0;
        }

        body.sidebar-collapsed .sidebar-nav .nav-link {
            justify-content: center;
            padding: 0.65rem;
            gap: 0;
        }

        body.sidebar-collapsed .sidebar-footer {
            display: flex;
            justify-content: center;
            padding: 0.75rem 0;
        }

        /* ── Main wrapper ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left var(--sidebar-transition);
        }

        body.sidebar-collapsed .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* ── Top navbar ── */
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
            min-height: 64px;
        }

        .top-navbar .page-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a202c;
        }

        /* Hamburger button (mobile only) */
        .hamburger-btn {
            background: transparent;
            border: none;
            color: #4a5568;
            padding: 0.25rem 0.5rem 0.25rem 0;
            cursor: pointer;
            display: none;
            align-items: center;
        }

        .hamburger-btn i { font-size: 1.4rem; }

        /* ── Mobile backdrop ── */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }

            body.sidebar-mobile-open .sidebar {
                transform: translateX(0);
            }

            .main-wrapper { margin-left: 0 !important; }

            /* Hide desktop collapse btn, show hamburger */
            .sidebar-collapse-btn { display: none !important; }
            .hamburger-btn { display: flex; }

            /* Always show text on mobile */
            .brand-text,
            .nav-link-text,
            .nav-badge,
            .nav-label,
            .footer-text {
                display: revert !important;
            }

            .sidebar-brand { justify-content: flex-start !important; padding: 1rem 1.25rem !important; }
            .sidebar-nav .nav-link { justify-content: flex-start !important; padding: 0.65rem 1.25rem !important; gap: 0.65rem !important; }
            .sidebar-footer { justify-content: flex-start !important; padding: 0.75rem 1.25rem !important; }
        }

        /* ── Cards ── */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            transition: transform 0.15s;
        }
        .stat-card:hover { transform: translateY(-2px); }

        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }

        .stat-value { font-size: 2rem; font-weight: 700; color: #1a202c; }
        .stat-label { font-size: 0.8rem; color: #718096; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }

        /* ── Table ── */
        .table-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .table thead th {
            background: #f7fafc;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .table tbody td { font-size: 0.875rem; vertical-align: middle; }
        .table tbody tr:hover { background-color: #f7fafc; }

        /* ── Charts ── */
        .chart-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 1.25rem;
        }

        .chart-card .chart-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }

        /* ── Badges ── */
        .badge-transaction { font-size: 0.75rem; font-weight: 500; }

        /* ── Filter bar ── */
        .filter-bar {
            background: #fff;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
            margin-bottom: 1rem;
        }

        /* ── Sort link ── */
        .sort-link { color: inherit; text-decoration: none; white-space: nowrap; }
        .sort-link:hover { color: var(--dost-blue); }
        .sort-link i { font-size: 0.7rem; }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── Mobile backdrop ── --}}
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    {{-- ── Sidebar ── --}}
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <div class="brand-text">
                <div class="brand-name">DOST Surigao del Norte</div>
                <div class="brand-sub">Client Visit Logbook</div>
            </div>
            <button class="sidebar-collapse-btn" id="sidebar-collapse-btn"
                    title="Collapse sidebar" aria-label="Toggle sidebar">
                <i class="bi bi-chevron-left" id="collapse-icon"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               title="Dashboard">
                <i class="bi bi-speedometer2"></i>
                <span class="nav-link-text">Dashboard</span>
            </a>

            <a href="{{ route('admin.pending.index') }}"
               class="nav-link {{ request()->routeIs('admin.pending.*') ? 'active' : '' }}"
               title="Pending{{ $pendingCount > 0 ? ' (' . $pendingCount . ')' : '' }}">
                <i class="bi bi-hourglass-split"></i>
                <span class="nav-link-text">Pending</span>
                @if($pendingCount > 0)
                    <span class="nav-badge">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.logs.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="nav-link" target="_blank" title="Print View">
                <i class="bi bi-printer"></i>
                <span class="nav-link-text">Print View</span>
            </a>

            <a href="{{ route('admin.export.csv') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="nav-link" title="Export CSV">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span class="nav-link-text">Export CSV</span>
            </a>

            <div class="nav-label mt-3">System</div>

            <a href="{{ route('logbook.index') }}" class="nav-link" target="_blank" title="Public Form">
                <i class="bi bi-box-arrow-up-right"></i>
                <span class="nav-link-text">Public Form</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start bg-transparent border-0"
                        title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="nav-link-text">Logout</span>
                </button>
            </form>
        </nav>

        <div class="sidebar-footer">
            <div class="footer-text">
                Logged in as:<br>
                <strong class="footer-name">{{ auth()->user()->name }}</strong>
            </div>
        </div>

    </aside>

    {{-- ── Main Content ── --}}
    <div class="main-wrapper" id="main-wrapper">

        {{-- Top Navbar --}}
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                {{-- Hamburger (mobile only) --}}
                <button class="hamburger-btn" id="hamburger-btn" aria-label="Open navigation">
                    <i class="bi bi-list"></i>
                </button>
                <span class="page-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                {{-- Notification bell --}}
                @if($pendingCount > 0)
                    <a href="{{ route('admin.pending.index') }}"
                       class="position-relative text-decoration-none"
                       title="{{ $pendingCount }} pending submission(s)">
                        <i class="bi bi-bell-fill" style="font-size:1.15rem; color:#f59e0b;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size:0.6rem; min-width:16px; padding:2px 5px;">
                            {{ $pendingCount }}
                        </span>
                    </a>
                @else
                    <i class="bi bi-bell text-secondary" style="font-size:1.15rem;" title="No pending submissions"></i>
                @endif
                <i class="bi bi-person-circle text-secondary"></i>
                <span class="d-none d-sm-inline" style="font-size:0.875rem; color:#4a5568;">{{ auth()->user()->email }}</span>
            </div>
        </div>

        {{-- Flash Messages --}}
        <div class="container-fluid px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        {{-- Page Body --}}
        <main class="container-fluid px-4 pb-4">
            @yield('content')
        </main>

    </div>{{-- /main-wrapper --}}

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function () {
        'use strict';

        const body        = document.body;
        const backdrop    = document.getElementById('sidebar-backdrop');
        const collapseBtn = document.getElementById('sidebar-collapse-btn');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const STORAGE_KEY = 'sdn_sidebar_collapsed';
        const DESKTOP_BP  = 992; // matches Bootstrap lg breakpoint

        // ── Desktop collapse ─────────────────────────────────────────────────

        function isDesktop() { return window.innerWidth >= DESKTOP_BP; }

        function setCollapsed(collapsed) {
            body.classList.toggle('sidebar-collapsed', collapsed);
            try { localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0'); } catch (_) {}
        }

        // Restore saved state on load (desktop only)
        if (isDesktop()) {
            try {
                if (localStorage.getItem(STORAGE_KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (_) {}
        }

        if (collapseBtn) {
            collapseBtn.addEventListener('click', function () {
                setCollapsed(!body.classList.contains('sidebar-collapsed'));
            });
        }

        // ── Mobile overlay ───────────────────────────────────────────────────

        function openMobile() {
            body.classList.add('sidebar-mobile-open');
            backdrop.classList.add('show');
            document.addEventListener('keydown', onEscape);
        }

        function closeMobile() {
            body.classList.remove('sidebar-mobile-open');
            backdrop.classList.remove('show');
            document.removeEventListener('keydown', onEscape);
        }

        function onEscape(e) { if (e.key === 'Escape') closeMobile(); }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function () {
                body.classList.contains('sidebar-mobile-open') ? closeMobile() : openMobile();
            });
        }

        backdrop.addEventListener('click', closeMobile);

        // Close mobile overlay when resizing to desktop
        window.addEventListener('resize', function () {
            if (isDesktop()) closeMobile();
        });
    }());
    </script>

    @stack('scripts')
</body>
</html>
