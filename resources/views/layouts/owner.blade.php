<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') – AquaSync Owner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
    :root {
        --sb-bg:#0d2b2b;--sb-hover:#163d3d;--sb-active:#0e7c7b;--sb-w:260px;
        --primary:#0e7c7b;--primary-lt:#e6f7f7;
        --success:#2dc653;--warning:#f4a261;--danger:#e63946;
        --body-bg:#f2f8f8;--card:#fff;
        --text:#1a2b2b;--muted:#5c7a7a;--border:#ddeaea;
        --shadow:0 1px 4px rgba(0,0,0,.06);--shadow-md:0 4px 16px rgba(0,0,0,.08);
        --r:12px;--r-sm:8px;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans',sans-serif;background:var(--body-bg);color:var(--text);min-height:100vh}

    /* ── Sidebar ── */
    .sb{position:fixed;top:0;left:0;bottom:0;width:var(--sb-w);background:var(--sb-bg);display:flex;flex-direction:column;z-index:100}
    .sb-brand{padding:22px 24px 18px;border-bottom:1px solid rgba(255,255,255,.08)}
    .sb-brand-name{font-size:1.2rem;font-weight:700;color:#fff}
    .sb-badge{display:inline-block;margin-top:6px;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;padding:3px 9px;border-radius:4px;background:rgba(14,124,123,.18);color:#5dd6d5;border:1px solid rgba(14,124,123,.35)}
    .sb-readonly{margin:10px 16px 0;padding:8px 12px;background:rgba(14,124,123,.08);border:1px solid rgba(14,124,123,.2);border-radius:var(--r-sm);display:flex;align-items:center;gap:7px;font-size:.73rem;color:#5dd6d5}
    .sb-nav{flex:1;overflow-y:auto;overflow-x:hidden;padding-bottom:12px}
    .sb-nav::-webkit-scrollbar{width:3px}
    .sb-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:3px}
    .sb-sec{padding:18px 16px 4px}
    .sb-sec-lbl{font-size:.63rem;font-weight:600;color:rgba(255,255,255,.32);text-transform:uppercase;letter-spacing:1px;padding:0 8px;margin-bottom:5px}
    .nav-link-a{display:flex;align-items:center;gap:10px;padding:9px 14px;border-radius:var(--r-sm);color:rgba(255,255,255,.68);text-decoration:none;font-size:.86rem;font-weight:500;transition:all .15s;margin:1px 8px}
    .nav-link-a:hover{background:var(--sb-hover);color:#fff}
    .nav-link-a.active{background:var(--sb-active);color:#fff;box-shadow:0 2px 8px rgba(14,124,123,.4)}
    .nav-icon{width:16px;height:16px;flex-shrink:0}
    .nav-badge{margin-left:auto;background:var(--danger);color:#fff;font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:10px}
    .sb-foot{padding:12px 16px;border-top:1px solid rgba(255,255,255,.08)}
    .btn-logout{display:flex;align-items:center;gap:10px;width:100%;padding:11px 14px;border-radius:var(--r-sm);background:#e63946;border:none;color:#fff;font-size:.86rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s}
    .btn-logout:hover{background:#c1121f}

    /* ── Main ── */
    .main-wrap{margin-left:var(--sb-w);min-height:100vh;display:flex;flex-direction:column}
    .topbar{height:64px;background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:50}
    .topbar-title{font-size:1.05rem;font-weight:700;color:var(--text)}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .btn-icon{width:38px;height:38px;border-radius:50%;border:none;background:var(--body-bg);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);position:relative;transition:background .15s;text-decoration:none}
    .btn-icon:hover{background:var(--primary-lt);color:var(--primary)}
    .notif-dot{position:absolute;top:5px;right:5px;width:7px;height:7px;background:var(--danger);border-radius:50%;border:2px solid var(--card)}
    .avatar{width:36px;height:36px;border-radius:50%;background:var(--primary);color:#fff;font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0}
    .page-body{flex:1;padding:28px}

    /* ── Cards ── */
    .card-a{background:var(--card);border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--shadow);padding:22px}
    .card-hd{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
    .card-ttl{font-size:.95rem;font-weight:700;color:var(--text)}
    .stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:20px 22px;box-shadow:var(--shadow);display:flex;justify-content:space-between;align-items:flex-start}
    .stat-lbl{font-size:.72rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px}
    .stat-val{font-size:1.9rem;font-weight:700;line-height:1}
    .stat-sub{font-size:.74rem;color:var(--muted);margin-top:6px}
    .stat-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ico-teal{background:rgba(14,124,123,.1);color:var(--primary)}
    .ico-green{background:rgba(45,198,83,.1);color:var(--success)}
    .ico-orange{background:rgba(244,162,97,.1);color:var(--warning)}
    .ico-red{background:rgba(230,57,70,.1);color:var(--danger)}

    /* ── Table ── */
    .tbl{width:100%;border-collapse:collapse}
    .tbl thead th{padding:10px 16px;font-size:.7rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--border);white-space:nowrap}
    .tbl tbody tr{border-bottom:1px solid var(--border);transition:background .1s}
    .tbl tbody tr:hover{background:var(--body-bg)}
    .tbl tbody td{padding:12px 16px;font-size:.875rem}
    .tbl tbody tr:last-child{border-bottom:none}

    /* ── Badges ── */
    .bdg{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600}
    .bdg-normal{background:#d4edda;color:#155724}
    .bdg-asam{background:#fff3cd;color:#856404}
    .bdg-basa{background:#cce5ff;color:#004085}
    .bdg-kritis{background:#f8d7da;color:#721c24}
    .bdg-aman{background:#d4edda;color:#155724}
    .bdg-warning{background:#fff3cd;color:#856404}
    .bdg-aktif{background:#d4edda;color:#155724}
    .bdg-nonaktif{background:#e2e8f0;color:#6b7c93}
    .bdg-menunggu{background:#e2e8f0;color:#4a5568}
    .bdg-flagged{background:#f8d7da;color:#721c24}
    .bdg-dibalas{background:#d4edda;color:#155724}
    .bdg-selesai{background:rgba(14,124,123,.1);color:var(--primary)}

    /* ── Forms ── */
    .flbl{font-size:.78rem;font-weight:600;color:var(--text);margin-bottom:6px;display:block}
    .finp{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-size:.875rem;font-family:'DM Sans',sans-serif;color:var(--text);background:var(--card);transition:border-color .15s,box-shadow .15s;outline:none}
    .finp:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(14,124,123,.1)}

    /* ── Buttons ── */
    .btn-p{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--primary);color:#fff;border:none;border-radius:var(--r-sm);font-size:.875rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:background .15s,transform .1s}
    .btn-p:hover{background:#0a5f5e;color:#fff;transform:translateY(-1px)}
    .btn-s{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--body-bg);color:var(--text);border:1.5px solid var(--border);border-radius:var(--r-sm);font-size:.875rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all .15s}
    .btn-s:hover{background:var(--primary-lt);border-color:var(--primary);color:var(--primary)}
    .btn-flag{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#fff5f5;color:var(--danger);border:1.5px solid #fecaca;border-radius:var(--r-sm);font-size:.86rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all .15s}
    .btn-flag:hover{background:var(--danger);color:#fff}

    /* ── Modal ── */
    .modal-a .modal-content{border:none;border-radius:var(--r);box-shadow:0 20px 60px rgba(0,0,0,.15)}
    .modal-a .modal-header{border-bottom:1px solid var(--border);padding:20px 24px 16px}
    .modal-a .modal-title{font-size:.95rem;font-weight:700}
    .modal-a .modal-body{padding:24px}
    .modal-a .modal-footer{border-top:1px solid var(--border);padding:16px 24px}

    /* ── Toast ── */
    .toast-wrap{position:fixed;top:76px;right:22px;z-index:9999;display:flex;flex-direction:column;gap:10px}
    .toast-i{min-width:270px;max-width:360px;padding:13px 17px;border-radius:var(--r-sm);background:var(--card);border-left:4px solid var(--primary);box-shadow:var(--shadow-md);font-size:.86rem;display:flex;align-items:center;gap:10px;animation:tIn .3s ease}
    .toast-i.s{border-color:var(--success)}.toast-i.e{border-color:var(--danger)}
    @keyframes tIn{from{opacity:0;transform:translateX(18px)}to{opacity:1;transform:translateX(0)}}

    /* ── Empty ── */
    .empty{text-align:center;padding:44px 24px;color:var(--muted)}
    .empty h3{font-size:.95rem;font-weight:600;color:var(--text);margin-bottom:6px}

    /* ── Misc ── */
    .ph{margin-bottom:22px}
    .ph h1{font-size:1.3rem;font-weight:700;letter-spacing:-.3px}
    .ph p{color:var(--muted);font-size:.875rem;margin-top:3px}
    .sl{font-size:.8rem;font-weight:600;color:var(--primary);text-decoration:none}
    .sl:hover{text-decoration:underline}
    .mono{font-family:'DM Mono',monospace}
    .live-dot{display:inline-block;width:7px;height:7px;background:var(--success);border-radius:50%;margin-right:5px;animation:lp 1.8s ease-in-out infinite}
    @keyframes lp{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)}}
    </style>
    @stack('styles')
</head>
<body>

<aside class="sb">
    <div class="sb-brand">
        <div class="sb-brand-name">〜 AquaSync</div>
        <span class="sb-badge">Owner View</span>
    </div>
    <div class="sb-readonly">
        <i data-lucide="eye" style="width:13px;height:13px;flex-shrink:0;"></i>
        Mode monitoring – hanya baca
    </div>
    <nav class="sb-nav">
        <div class="sb-sec">
            <a href="{{ route('owner.dashboard') }}" class="nav-link-a {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i> Dashboard
            </a>
        </div>
        <div class="sb-sec">
            <div class="sb-sec-lbl">Monitoring</div>
            <a href="{{ route('owner.monitoring.kualitas-air') }}" class="nav-link-a {{ request()->routeIs('owner.monitoring.kualitas-air*') ? 'active' : '' }}">
                <i data-lucide="activity" class="nav-icon"></i> Kualitas Air
            </a>
            <a href="{{ route('owner.monitoring.jadwal') }}" class="nav-link-a {{ request()->routeIs('owner.monitoring.jadwal') ? 'active' : '' }}">
                <i data-lucide="calendar-clock" class="nav-icon"></i> Jadwal Pakan
            </a>
            <a href="{{ route('owner.monitoring.pengurasan') }}" class="nav-link-a {{ request()->routeIs('owner.monitoring.pengurasan') ? 'active' : '' }}">
                <i data-lucide="droplets" class="nav-icon"></i> Pengurasan Air
            </a>
            <a href="{{ route('owner.monitoring.panen') }}" class="nav-link-a {{ request()->routeIs('owner.monitoring.panen') ? 'active' : '' }}">
                <i data-lucide="package" class="nav-icon"></i> Jadwal Panen
            </a>
        </div>
        <div class="sb-sec">
            <div class="sb-sec-lbl">Interaksi</div>
            <a href="{{ route('owner.laporan.index') }}" class="nav-link-a {{ request()->routeIs('owner.laporan.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="nav-icon"></i> Laporan
            </a>
            <a href="{{ route('owner.komentar.index') }}" class="nav-link-a {{ request()->routeIs('owner.komentar.*') ? 'active' : '' }}">
                <i data-lucide="message-square" class="nav-icon"></i> Komentar & Flag
                @php $fc = \App\Models\KomentarFlag::where('user_id',auth()->id())->where('status','menunggu')->count() @endphp
                @if($fc)<span class="nav-badge" style="background:var(--primary);">{{ $fc }}</span>@endif
            </a>
            <a href="{{ route('owner.notifikasi.index') }}" class="nav-link-a {{ request()->routeIs('owner.notifikasi.*') ? 'active' : '' }}">
                <i data-lucide="bell" class="nav-icon"></i> Notifikasi
                @php $nc = \App\Models\Notifikasi::where('user_id',auth()->id())->where('is_read',false)->count() @endphp
                @if($nc)<span class="nav-badge">{{ $nc }}</span>@endif
            </a>
        </div>
        <div class="sb-sec">
            <div class="sb-sec-lbl">Akun</div>
            <a href="{{ route('owner.akun.index') }}" class="nav-link-a {{ request()->routeIs('owner.akun.*') ? 'active' : '' }}">
                <i data-lucide="user" class="nav-icon"></i> Profil Saya
            </a>
        </div>
    </nav>
    <div class="sb-foot">
        <form action="{{ route('logout') }}" method="POST">@csrf
            <button type="submit" class="btn-logout">
                <i data-lucide="log-out" style="width:15px;height:15px;"></i> Logout
            </button>
        </form>
    </div>
</aside>

<div class="main-wrap">
    <header class="topbar">
        <span class="topbar-title">@yield('page-title','Dashboard')</span>
        <div class="topbar-right">
            <a href="{{ route('owner.notifikasi.index') }}" class="btn-icon">
                <i data-lucide="bell" style="width:17px;height:17px;"></i>
                @if($nc ?? 0)<span class="notif-dot"></span>@endif
            </a>
            <div class="dropdown">
                <div class="avatar" data-bs-toggle="dropdown">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:176px;border-radius:10px;border:1px solid var(--border);box-shadow:var(--shadow-md);padding:8px;">
                    <li><span class="dropdown-item-text" style="font-size:.76rem;color:var(--muted);padding:5px 13px;">
                        {{ auth()->user()->name }}<br><strong style="color:var(--primary);">Owner</strong>
                    </span></li>
                    <li><hr class="dropdown-divider" style="margin:4px 0;"></li>
                    <li><a href="{{ route('owner.akun.index') }}" class="dropdown-item" style="font-size:.86rem;border-radius:6px;">
                        <i data-lucide="user" style="width:13px;height:13px;margin-right:6px;"></i>Profil Saya
                    </a></li>
                    <li><form action="{{ route('logout') }}" method="POST">@csrf
                        <button class="dropdown-item" style="font-size:.86rem;border-radius:6px;width:100%;text-align:left;">
                            <i data-lucide="log-out" style="width:13px;height:13px;margin-right:6px;"></i>Logout
                        </button>
                    </form></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="page-body">
        @if(session('success') || session('error'))
        <div class="toast-wrap" id="tw">
            @if(session('success'))<div class="toast-i s"><i data-lucide="check-circle" style="width:17px;height:17px;color:var(--success);flex-shrink:0;"></i>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="toast-i e"><i data-lucide="x-circle" style="width:17px;height:17px;color:var(--danger);flex-shrink:0;"></i>{{ session('error') }}</div>@endif
        </div>
        @endif
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
lucide.createIcons();
setTimeout(()=>{const t=document.getElementById('tw');if(t){t.style.opacity='0';t.style.transition='opacity .4s';setTimeout(()=>t.remove(),400)}},4000);
</script>
@stack('scripts')
</body>
</html>