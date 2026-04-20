<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Client Visit Logbook') — DOST Surigao del Norte</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Fonts: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --dost-blue:       #003a8c;
            --dost-blue-light: #1565c0;
            --dost-gold:       #f5a623;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            color: #1a202c;
        }

        /* ── Header ── */
        .site-header {
            background: linear-gradient(135deg, var(--dost-blue) 0%, var(--dost-blue-light) 100%);
            padding: 0.75rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .site-header .agency-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.02em;
        }

        .site-header .agency-sub {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.75);
        }

        /* ── Content card ── */
        .content-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            padding: 2.5rem;
        }

        /* ── Footer ── */
        .site-footer {
            background: var(--dost-blue);
            color: rgba(255,255,255,0.65);
            font-size: 0.8rem;
            padding: 1rem 0;
            margin-top: 3rem;
        }

        /* ── Form styling ── */
        .form-label { font-weight: 500; color: #374151; }
        .form-control:focus, .form-select:focus {
            border-color: var(--dost-blue-light);
            box-shadow: 0 0 0 0.2rem rgba(21,101,192,0.2);
        }

        .btn-primary {
            background-color: var(--dost-blue);
            border-color:     var(--dost-blue);
        }
        .btn-primary:hover {
            background-color: var(--dost-blue-light);
            border-color:     var(--dost-blue-light);
        }

        /* ── Alert overrides ── */
        .alert-danger { border-left: 4px solid #dc3545; }
        .alert-success { border-left: 4px solid #198754; }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── Site Header ── --}}
    <header class="site-header">
        <div class="container">
            <div class="d-flex align-items-center gap-3">
                {{-- DOST Logo placeholder (replace src with actual logo path) --}}
                <img src="{{ asset('images/dost-logo.png') }}"
                     alt="DOST Logo"
                     style="height:48px; width:48px; object-fit:contain;"
                     onerror="this.style.display='none'">
                <div>
                    <div class="agency-name">Department of Science and Technology</div>
                    <div class="agency-sub">Surigao del Norte</div>
                </div>
            </div>
        </div>
    </header>

    {{-- ── Page Content ── --}}
    <main class="container py-4">
        @yield('content')
    </main>

    {{-- ── Footer ── --}}
    <footer class="site-footer text-center">
        <div class="container">
            &copy; {{ date('Y') }} DOST Surigao del Norte. All rights reserved.
        </div>
    </footer>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
