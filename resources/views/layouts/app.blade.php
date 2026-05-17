<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quiz System')</title>

    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:   #4f46e5;
            --primary-h: #4338ca;
            --success:   #16a34a;
            --danger:    #dc2626;
            --warning:   #d97706;
            --bg:        #f1f5f9;
        }

        body {
            background: var(--bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: .5px;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-h);
            border-color: var(--primary-h);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .progress-bar {
            background: var(--primary);
        }

        /* Answer option cards */
        .answer-option {
            cursor: pointer;
            border: 2px solid #e2e8f0;
            border-radius: .75rem;
            transition: border-color .2s, background .2s;
            padding: 1rem 1.25rem;
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .answer-option:hover {
            border-color: var(--primary);
            background: #eef2ff;
        }

        .answer-option input[type="radio"] {
            accent-color: var(--primary);
            width: 1.1rem;
            height: 1.1rem;
            flex-shrink: 0;
        }

        .answer-option.selected {
            border-color: var(--primary);
            background: #eef2ff;
        }

        /* Result badges */
        .badge-correct  { background: var(--success); }
        .badge-wrong    { background: var(--danger);  }
        .badge-skipped  { background: var(--warning); }

        /* Stat cards */
        .stat-card {
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            color: #fff;
        }

        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: .9rem;
            opacity: .9;
            margin-top: .25rem;
        }

        #loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #loading-overlay.active {
            display: flex;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary);">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="bi bi-patch-question-fill me-2"></i>Quiz System
            </a>
        </div>
    </nav>

    <!-- Loading overlay -->
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading…</span>
        </div>
    </div>

    <!-- Main content -->
    <main class="py-5">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
