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
            --dost-blue:       #003a8c;
            --dost-blue-light: #1565c0;
            --sidebar-width:   260px;
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
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar-brand .brand-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.6);
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
        }

        .sidebar-nav .nav-label {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.4);
            padding: 0.5rem 1.25rem 0.25rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 1.25rem;
            color: rgba(255,255,255,0.75);
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 0;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #63b3ed;
        }

        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.2rem; }

        /* ── Main wrapper ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        }

        .top-navbar .page-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a202c;
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

        /* ── Responsive: hide sidebar on small screens ── */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-name">DOST Surigao del Norte</div>
            <div class="brand-sub">Client Visit Logbook</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <a href="{{ route('admin.pending.index') }}"
               class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.pending.*') ? 'active' : '' }}">
                <span><i class="bi bi-hourglass-split"></i> Pending</span>
                @if($pendingCount > 0)
                    <span class="badge rounded-pill" style="background:#ef4444; font-size:0.68rem; min-width:20px;">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.logs.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="nav-link" target="_blank">
                <i class="bi bi-printer"></i> Print View
            </a>

            <a href="{{ route('admin.export.csv') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="nav-link">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>

            <div class="nav-label mt-3">System</div>

            <a href="{{ route('logbook.index') }}" class="nav-link" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> Public Form
            </a>

            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="nav-link w-100 text-start bg-transparent border-0">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </nav>

        <div class="p-3" style="border-top:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:0.75rem; color:rgba(255,255,255,0.45);">
                Logged in as:<br>
                <strong style="color:rgba(255,255,255,0.75);">{{ auth()->user()->name }}</strong>
            </div>
        </div>
    </aside>

    {{-- ── Main Content ── --}}
    <div class="main-wrapper">

        {{-- Top Navbar --}}
        <div class="top-navbar">
            <span class="page-title">@yield('page-title', 'Dashboard')</span>
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
                <span style="font-size:0.875rem; color:#4a5568;">{{ auth()->user()->email }}</span>
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

    @stack('scripts')
</body>
</html>
