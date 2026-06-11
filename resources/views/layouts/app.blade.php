<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0ea5e9">

    <link rel="manifest" href="/manifest.json">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="AquaSync">

    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>@yield('title', 'Dashboard') – AquaSync</title>

    {{-- Google Fonts: DM Sans + DM Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>

    <style>
        :root {
            --sidebar-bg:     #0f2942;
            --sidebar-hover:  #1a3a5c;
            --sidebar-active: #1d6fa4;
            --sidebar-width:  260px;
            --primary:        #1d6fa4;
            --primary-light:  #e8f4fd;
            --accent:         #e63946;
            --success:        #2dc653;
            --warning:        #f4a261;
            --danger:         #e63946;
            --body-bg:        #f4f7fb;
            --card-bg:        #ffffff;
            --text-main:      #1a2b3c;
            --text-muted:     #6b7c93;
            --border:         #e2e8f0;
            --shadow-sm:      0 1px 4px rgba(0,0,0,.06);
            --shadow-md:      0 4px 16px rgba(0,0,0,.08);
            --radius:         12px;
            --radius-sm:      8px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ── SIDEBAR ─────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 22px 24px 18px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.3px;
        }
        .sidebar-brand .brand-wave {
            font-size: 1.3rem;
            margin-right: 6px;
        }
        .sidebar-brand .brand-sub {
            font-size: .68rem;
            color: rgba(255,255,255,.45);
            font-weight: 400;
            display: block;
            margin-top: 2px;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .sidebar-section {
            padding: 20px 16px 6px;
        }
        .sidebar-section-label {
            font-size: .65rem;
            font-weight: 600;
            color: rgba(255,255,255,.35);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 8px;
            margin-bottom: 6px;
        }

        .sidebar-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding-bottom: 12px; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 4px; }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: all .18s ease;
            margin: 1px 8px;
        }
        .nav-item-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .nav-item-link.active {
            background: var(--sidebar-active);
            color: #fff;
            box-shadow: 0 2px 8px rgba(29,111,164,.4);
        }
        .nav-item-link .nav-icon {
            width: 18px; height: 18px;
            flex-shrink: 0;
            opacity: .85;
        }
        .nav-item-link.active .nav-icon { opacity: 1; }

        .sidebar-footer {
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            background: #e63946;
            border: none;
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .18s;
        }
        .btn-logout:hover { background: #c1121f; color: #fff; }

        /* ── MAIN CONTENT ────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ──────────────────────────────── */
        .topbar {
            height: 64px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -.2px;
        }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        .btn-icon {
            width: 38px; height: 38px;
            border-radius: 50%;
            border: none;
            background: var(--body-bg);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            position: relative;
            transition: background .15s;
        }
        .btn-icon:hover { background: var(--primary-light); color: var(--primary); }

        .notif-badge {
            position: absolute;
            top: 4px; right: 4px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--card-bg);
        }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            font-size: .8rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
        }

        /* ── PAGE CONTENT ────────────────────────── */
        .page-content {
            flex: 1;
            padding: 28px;
        }
        .page-header {
            margin-bottom: 24px;
        }
        .page-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -.3px;
        }
        .page-header p {
            color: var(--text-muted);
            font-size: .875rem;
            margin-top: 4px;
        }

        /* ── CARDS ───────────────────────────────── */
        .card-aqua {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 22px;
        }
        .card-aqua .card-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .card-aqua .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Summary stat cards */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .stat-card .stat-label {
            font-size: .78rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 8px;
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1;
        }
        .stat-card .stat-sub {
            font-size: .75rem;
            color: var(--text-muted);
            margin-top: 6px;
        }
        .stat-icon-wrap {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* ── BADGES ──────────────────────────────── */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .73rem;
            font-weight: 600;
        }
        .badge-normal  { background: #d4edda; color: #155724; }
        .badge-asam    { background: #fff3cd; color: #856404; }
        .badge-basa    { background: #cce5ff; color: #004085; }
        .badge-kritis  { background: #f8d7da; color: #721c24; }
        .badge-aman    { background: #d4edda; color: #155724; }
        .badge-butuh-kuras { background: #fff3cd; color: #856404; }
        .badge-flagged { background: #f8d7da; color: #721c24; }
        .badge-menunggu { background: #e2e8f0; color: #4a5568; }
        .badge-selesai { background: #d4edda; color: #155724; }
        .badge-aktif   { background: #d4edda; color: #155724; }
        .badge-nonaktif{ background: #e2e8f0; color: #4a5568; }

        /* ── TABLE ───────────────────────────────── */
        .table-aqua { width: 100%; border-collapse: collapse; }
        .table-aqua thead th {
            padding: 11px 16px;
            font-size: .72rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .table-aqua tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        .table-aqua tbody tr:hover { background: var(--body-bg); }
        .table-aqua tbody td {
            padding: 13px 16px;
            font-size: .875rem;
            color: var(--text-main);
        }
        .table-aqua tbody tr:last-child { border-bottom: none; }

        /* ── FORMS ───────────────────────────────── */
        .form-label-aqua {
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
            display: block;
        }
        .form-input-aqua {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-main);
            background: var(--card-bg);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-input-aqua:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(29,111,164,.12);
        }

        /* ── BUTTONS ─────────────────────────────── */
        .btn-primary-aqua {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, transform .1s;
            text-decoration: none;
        }
        .btn-primary-aqua:hover { background: #155e8e; color: #fff; transform: translateY(-1px); }

        .btn-secondary-aqua {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            background: var(--body-bg);
            color: var(--text-main);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }
        .btn-secondary-aqua:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }

        .btn-danger-aqua {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 7px 14px;
            background: #fff5f5;
            color: var(--danger);
            border: 1.5px solid #fecaca;
            border-radius: var(--radius-sm);
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }
        .btn-danger-aqua:hover { background: var(--danger); color: #fff; }

        /* ── ALERT TOAST ─────────────────────────── */
        .toast-stack {
            position: fixed; top: 78px; right: 24px;
            z-index: 9999; display: flex; flex-direction: column; gap: 10px;
        }
        .toast-item {
            min-width: 280px; max-width: 360px;
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            background: var(--card-bg);
            border-left: 4px solid var(--primary);
            box-shadow: var(--shadow-md);
            font-size: .875rem;
            animation: toastIn .3s ease;
            display: flex; align-items: center; gap: 10px;
        }
        .toast-item.success { border-color: var(--success); }
        .toast-item.error   { border-color: var(--danger); }
        .toast-item.warning { border-color: var(--warning); }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── MODAL ───────────────────────────────── */
        .modal-aqua .modal-content {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
        }
        .modal-aqua .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 20px 24px 16px;
        }
        .modal-aqua .modal-title {
            font-size: 1rem; font-weight: 700;
        }
        .modal-aqua .modal-body { padding: 24px; }
        .modal-aqua .modal-footer {
            border-top: 1px solid var(--border);
            padding: 16px 24px;
        }

        /* ── EMPTY STATE ─────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }
        .empty-state svg { margin-bottom: 16px; opacity: .3; }
        .empty-state h3 { font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .empty-state p  { font-size: .875rem; }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }

        /* ── UTILITIES ───────────────────────────── */
        .text-primary-aqua { color: var(--primary) !important; }
        .text-muted-aqua   { color: var(--text-muted) !important; }
        .gap-2 { gap: .5rem; }
        .gap-3 { gap: .75rem; }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-name">
            <span class="brand-wave">〜</span> AquaSync
        </div>
        <span class="brand-sub">Sistem Monitoring Kolam Lele</span>
    </div>

    {{-- Nav --}}
    <nav class="sidebar-nav">
        {{-- Dashboard --}}
        <div class="sidebar-section">
            <a href="{{ route('dashboard') }}"
               class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                Dashboard
            </a>
        </div>

        {{-- Manajemen Utama --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Manajemen Utama</div>

            <a href="{{ route('kolam.index') }}"
               class="nav-item-link {{ request()->routeIs('kolam.*') ? 'active' : '' }}">
                <i data-lucide="grid-2x2" class="nav-icon"></i>
                Data Kolam
            </a>

            <a href="{{ route('kualitas-air.index') }}"
               class="nav-item-link {{ request()->routeIs('kualitas-air.*') ? 'active' : '' }}">
                <i data-lucide="activity" class="nav-icon"></i>
                Kualitas Air
            </a>

            <a href="{{ route('jadwal-pakan.index') }}"
               class="nav-item-link {{ request()->routeIs('jadwal-pakan.*') ? 'active' : '' }}">
                <i data-lucide="calendar-clock" class="nav-icon"></i>
                Jadwal Pakan
            </a>

            <a href="{{ route('pengurasan-air.index') }}"
               class="nav-item-link {{ request()->routeIs('pengurasan-air.*') ? 'active' : '' }}">
                <i data-lucide="droplets" class="nav-icon"></i>
                Pengurasan Air
            </a>

            <a href="{{ route('jadwal-panen.index') }}"
               class="nav-item-link {{ request()->routeIs('jadwal-panen.*') ? 'active' : '' }}">
                <i data-lucide="package" class="nav-icon"></i>
                Panen
            </a>
        </div>

        {{-- Sistem --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Sistem</div>

            <a href="{{ route('laporan.index') }}"
               class="nav-item-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="nav-icon"></i>
                Laporan
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('flag.index') }}"
               class="nav-item-link {{ request()->routeIs('flag.*') ? 'active' : '' }}">
                <i data-lucide="flag" class="nav-icon"></i>
                Komentar & Flag
                @php $flagCount = \App\Models\KomentarFlag::where('status','menunggu')->count() @endphp
                @if($flagCount > 0)
                    <span style="margin-left:auto;background:var(--danger);color:#fff;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:10px;">{{ $flagCount }}</span>
                @endif
            </a>
            <a href="#" class="nav-item-link">
                <i data-lucide="user-cog" class="nav-icon"></i>
                Akun Admin
            </a>
            @endif
        </div>
    </nav>

    {{-- Footer: Logout --}}
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- MAIN WRAPPER --}}
<div class="main-wrapper">

    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-icon d-md-none" onclick="toggleSidebar()">
                <i data-lucide="menu" style="width:18px;height:18px;"></i>
            </button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-actions">
            {{-- Notifikasi --}}
            <a href="{{ route('notifikasi.index') }}" class="btn-icon" style="text-decoration:none;">
                <i data-lucide="bell" style="width:18px;height:18px;"></i>
                @php $unread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('is_read',false)->count() @endphp
                @if($unread > 0)
                    <span class="notif-badge"></span>
                @endif
            </a>
            {{-- User --}}
            <div class="dropdown">
                <div class="user-avatar" data-bs-toggle="dropdown" style="cursor:pointer;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:180px;border-radius:10px;border:1px solid var(--border);box-shadow:var(--shadow-md);padding:8px;">
                    <li><span class="dropdown-item-text" style="font-size:.78rem;color:var(--text-muted);padding:6px 14px;">
                        {{ auth()->user()->name }}<br>
                        <strong style="color:var(--primary);text-transform:capitalize;">{{ auth()->user()->role }}</strong>
                    </span></li>
                    <li><hr class="dropdown-divider" style="margin:4px 0;"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item" style="font-size:.875rem;border-radius:6px;">
                                <i data-lucide="log-out" style="width:14px;height:14px;margin-right:6px;"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="page-content">

        {{-- Toast Alerts --}}
        @if(session('success') || session('error') || session('warning'))
        <div class="toast-stack" id="toastStack">
            @if(session('success'))
            <div class="toast-item success">
                <i data-lucide="check-circle" style="width:18px;height:18px;color:var(--success);flex-shrink:0;"></i>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="toast-item error">
                <i data-lucide="x-circle" style="width:18px;height:18px;color:var(--danger);flex-shrink:0;"></i>
                {{ session('error') }}
            </div>
            @endif
            @if(session('warning'))
            <div class="toast-item warning">
                <i data-lucide="alert-triangle" style="width:18px;height:18px;color:var(--warning);flex-shrink:0;"></i>
                {{ session('warning') }}
            </div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Init Lucide icons
    lucide.createIcons();

    // Auto-dismiss toast
    setTimeout(() => {
        const stack = document.getElementById('toastStack');
        if (stack) stack.style.opacity = '0', stack.style.transition = 'opacity .4s', setTimeout(() => stack.remove(), 400);
    }, 4000);

    // Mobile sidebar toggle
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }
</script>
@stack('scripts')
</body>
</html>