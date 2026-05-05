@extends('layouts.owner')
@section('title','Akun')
@section('page-title','Akun')

@section('content')

{{-- ═══════════════════════════════════════════
     FLASH MESSAGES
═══════════════════════════════════════════ --}}
@if(session('success'))
<div class="flash flash-success" id="flash-msg">
    <i data-lucide="check-circle" class="flash-ic"></i>
    <span>{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()" class="flash-close">
        <i data-lucide="x" style="width:14px;height:14px"></i>
    </button>
</div>
@endif

@if(session('error'))
<div class="flash flash-error" id="flash-msg">
    <i data-lucide="alert-circle" class="flash-ic"></i>
    <span>{{ session('error') }}</span>
    <button onclick="this.parentElement.remove()" class="flash-close">
        <i data-lucide="x" style="width:14px;height:14px"></i>
    </button>
</div>
@endif

@if($errors->any())
<div class="flash flash-error">
    <i data-lucide="alert-triangle" class="flash-ic"></i>
    <div style="flex:1">
        <div style="font-weight:600;margin-bottom:4px">Terdapat kesalahan:</div>
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $error)
                <li style="font-size:.83rem">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button onclick="this.parentElement.remove()" class="flash-close">
        <i data-lucide="x" style="width:14px;height:14px"></i>
    </button>
</div>
@endif

{{-- ═══════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════ --}}
<div class="ph">
    <h1>Akun</h1>
    <p>Kelola akun dan keamanan akun Owner &amp; Admin</p>
</div>

<div class="row g-3">

    {{-- ═══════════════════════════
         COL KIRI — INFO PROFIL
    ═══════════════════════════ --}}
    <div class="col-12 col-lg-5">

        {{-- Informasi Akun --}}
        <div class="card-a">
            <div class="card-hd">
                <div class="card-ttl">Informasi Akun</div>
            </div>

            {{-- Avatar + nama --}}
            <div class="profile-hero">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="profile-meta">
                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-email">{{ $user->email }}</div>
                    <span class="bdg bdg-selesai" style="margin-top:6px;display:inline-block">
                        <i data-lucide="shield" style="width:11px;height:11px;margin-right:3px;vertical-align:middle"></i>Owner
                    </span>
                </div>
            </div>

            {{-- Detail baris --}}
            <div class="info-list">
                @foreach([
                    ['Email',      $user->email,                              'mail'],
                    ['No. HP',     $user->phone ?? '–',                       'phone'],
                    ['Role',       'Owner / Pemilik',                         'user-check'],
                    ['Status',     $user->is_active ? 'Aktif' : 'Nonaktif',  'activity'],
                    ['Bergabung',  $user->created_at->format('d M Y'),        'calendar'],
                ] as [$label, $value, $icon])
                <div class="info-row">
                    <span class="info-label">
                        <i data-lucide="{{ $icon }}" style="width:13px;height:13px;margin-right:5px;vertical-align:middle;color:var(--primary)"></i>
                        {{ $label }}
                    </span>
                    <span class="info-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>

            <div class="info-note">
                <i data-lucide="info" style="width:13px;height:13px;flex-shrink:0;color:var(--primary)"></i>
                Untuk mengubah nama atau email, hubungi Admin sistem.
            </div>
        </div>

        {{-- Aktivitas Saya --}}
        <div class="card-a mt-3">
            <div class="card-hd">
                <div class="card-ttl">Aktivitas Saya</div>
            </div>
            @php
                $totalFlag   = \App\Models\KomentarFlag::where('user_id', auth()->id())->count();
                $flagPending = \App\Models\KomentarFlag::where('user_id', auth()->id())->where('status','menunggu')->count();
                $flagDibalas = \App\Models\KomentarFlag::where('user_id', auth()->id())->where('status','dibalas')->count();
                $totalNotif  = \App\Models\Notifikasi::where('user_id', auth()->id())->count();
            @endphp
            <div class="stat-grid">
                @foreach([
                    ['Total Flag',       $totalFlag,   'message-square', 'primary'],
                    ['Menunggu Balasan', $flagPending, 'clock',          'warning'],
                    ['Sudah Dibalas',    $flagDibalas, 'check-circle',   'success'],
                    ['Total Notifikasi', $totalNotif,  'bell',           'info'],
                ] as [$label, $val, $ic, $color])
                <div class="stat-box stat-{{ $color }}">
                    <i data-lucide="{{ $ic }}" class="stat-icon"></i>
                    <div class="stat-num">{{ $val }}</div>
                    <div class="stat-label">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /col kiri --}}

    {{-- ═══════════════════════════
         COL KANAN — PASSWORD
    ═══════════════════════════ --}}
    <div class="col-12 col-lg-7">

        {{-- ── Ganti Password Sendiri ── --}}
        <div class="card-a">
            <div class="card-hd">
                <div class="card-ttl">
                    <i data-lucide="lock" style="width:15px;height:15px;margin-right:6px;vertical-align:middle;color:var(--primary)"></i>
                    Ganti Password Saya
                </div>
            </div>

            <form action="{{ route('owner.akun.password') }}" method="POST" id="form-pw-owner" novalidate>
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="flbl">Password Saat Ini <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="current_password" id="pw-current"
                                   class="finp @error('current_password') is-invalid @enderror"
                                   required placeholder="Masukkan password saat ini">
                            <button type="button" class="pw-eye" onclick="togglePw('pw-current',this)">
                                <i data-lucide="eye" style="width:15px;height:15px"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="field-err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="flbl">Password Baru <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password" id="pw-new"
                                   class="finp @error('password') is-invalid @enderror"
                                   required placeholder="Min. 8 karakter"
                                   oninput="checkStrength(this.value)">
                            <button type="button" class="pw-eye" onclick="togglePw('pw-new',this)">
                                <i data-lucide="eye" style="width:15px;height:15px"></i>
                            </button>
                        </div>
                        {{-- Strength meter --}}
                        <div class="strength-bar mt-1">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <div class="strength-text" id="strength-text"></div>
                        @error('password')
                            <div class="field-err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="flbl">Konfirmasi Password Baru <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password_confirmation" id="pw-confirm"
                                   class="finp"
                                   required placeholder="Ulangi password baru">
                            <button type="button" class="pw-eye" onclick="togglePw('pw-confirm',this)">
                                <i data-lucide="eye" style="width:15px;height:15px"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn-p">
                            <i data-lucide="save" style="width:15px;height:15px"></i>
                            Simpan Password Baru
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Kelola Password Admin ── --}}
        <div class="card-a mt-3">
            <div class="card-hd">
                <div class="card-ttl">
                    <i data-lucide="users" style="width:15px;height:15px;margin-right:6px;vertical-align:middle;color:var(--primary)"></i>
                    Kelola Password Admin
                </div>
                <span class="bdg" style="background:var(--primary-soft,#ede9fe);color:var(--primary);font-size:.72rem">
                    Hak Akses Owner
                </span>
            </div>

            <p style="font-size:.82rem;color:var(--muted);margin-bottom:14px">
                Sebagai Owner, Anda dapat mereset password akun Admin tanpa perlu mengetahui password lama mereka.
            </p>

            @forelse($admins as $admin)
            <div class="admin-row">
                {{-- Avatar admin --}}
                <div class="admin-avatar">
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                </div>
                <div class="admin-info">
                    <div class="admin-name">{{ $admin->name }}</div>
                    <div class="admin-email">{{ $admin->email }}</div>
                    <span class="bdg" style="font-size:.68rem;margin-top:3px;display:inline-block">Admin</span>
                </div>
                <div class="admin-actions">
                    {{-- Status badge --}}
                    @if($admin->is_active)
                        <span class="bdg bdg-selesai" style="font-size:.68rem">Aktif</span>
                    @else
                        <span class="bdg bdg-batal" style="font-size:.68rem">Nonaktif</span>
                    @endif
                    <button type="button" class="btn-p btn-sm"
                            onclick="openModal('modal-admin-{{ $admin->id }}')">
                        <i data-lucide="key" style="width:13px;height:13px"></i>
                        Reset
                    </button>
                </div>
            </div>

            {{-- ══ Modal Reset Password Admin ══ --}}
            <div class="modal-overlay" id="modal-admin-{{ $admin->id }}" onclick="closeModalOverlay(event, 'modal-admin-{{ $admin->id }}')">
                <div class="modal-box">
                    {{-- Header modal --}}
                    <div class="modal-hd">
                        <div>
                            <div class="modal-title">
                                <i data-lucide="key" style="width:16px;height:16px;margin-right:6px;color:var(--primary)"></i>
                                Reset Password Admin
                            </div>
                            <div class="modal-sub">{{ $admin->name }} &bull; {{ $admin->email }}</div>
                        </div>
                        <button type="button" class="modal-close" onclick="closeModal('modal-admin-{{ $admin->id }}')">
                            <i data-lucide="x" style="width:16px;height:16px"></i>
                        </button>
                    </div>

                    {{-- Peringatan --}}
                    <div class="modal-warn">
                        <i data-lucide="alert-triangle" style="width:14px;height:14px;flex-shrink:0;color:#d97706"></i>
                        Password lama admin akan langsung diganti. Pastikan Anda menginformasikan password baru kepada admin tersebut.
                    </div>

                    <form action="{{ route('owner.akun.admin.password', $admin) }}" method="POST" novalidate>
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="flbl">Password Baru <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" name="password"
                                           id="pw-admin-new-{{ $admin->id }}"
                                           class="finp" required placeholder="Min. 8 karakter"
                                           oninput="checkStrengthEl(this.value,'str-admin-{{ $admin->id }}','str-text-admin-{{ $admin->id }}')">
                                    <button type="button" class="pw-eye"
                                            onclick="togglePw('pw-admin-new-{{ $admin->id }}',this)">
                                        <i data-lucide="eye" style="width:15px;height:15px"></i>
                                    </button>
                                </div>
                                <div class="strength-bar mt-1">
                                    <div class="strength-fill" id="str-admin-{{ $admin->id }}"></div>
                                </div>
                                <div class="strength-text" id="str-text-admin-{{ $admin->id }}"></div>
                            </div>

                            <div class="col-12">
                                <label class="flbl">Konfirmasi Password Baru <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" name="password_confirmation"
                                           id="pw-admin-confirm-{{ $admin->id }}"
                                           class="finp" required placeholder="Ulangi password baru">
                                    <button type="button" class="pw-eye"
                                            onclick="togglePw('pw-admin-confirm-{{ $admin->id }}',this)">
                                        <i data-lucide="eye" style="width:15px;height:15px"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12" style="display:flex;gap:8px;flex-wrap:wrap">
                                <button type="submit" class="btn-p">
                                    <i data-lucide="save" style="width:14px;height:14px"></i>
                                    Simpan Password
                                </button>
                                <button type="button" class="btn-sec"
                                        onclick="closeModal('modal-admin-{{ $admin->id }}')">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- ══ End Modal ══ --}}

            @empty
            <div class="empty-state">
                <i data-lucide="users" style="width:32px;height:32px;color:var(--muted);display:block;margin:0 auto 8px"></i>
                <div style="color:var(--muted);font-size:.85rem">Belum ada akun Admin yang terdaftar.</div>
            </div>
            @endforelse
        </div>

    </div>{{-- /col kanan --}}
</div>

{{-- ═══════════════════════════════════════════
     SCOPED STYLES
═══════════════════════════════════════════ --}}
<style>
/* ── Flash ── */
.flash {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: var(--r-sm);
    margin-bottom: 18px;
    font-size: .86rem;
    animation: slideDown .25s ease;
}
.flash-success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; }
.flash-error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
.flash-ic      { width:16px; height:16px; flex-shrink:0; margin-top:1px; }
.flash-close   { margin-left:auto; background:none; border:none; cursor:pointer; padding:0; color:inherit; opacity:.6; }
.flash-close:hover { opacity:1; }
@keyframes slideDown { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

/* ── Profile hero ── */
.profile-hero {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--border);
}
.profile-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
}
.profile-name  { font-weight: 700; font-size: 1rem; }
.profile-email { font-size: .82rem; color: var(--muted); margin-top: 2px; }

/* ── Info list ── */
.info-list { display: flex; flex-direction: column; gap: 0; }
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    border-bottom: 1px solid var(--border);
}
.info-row:last-child { border-bottom: none; }
.info-label { font-size: .82rem; color: var(--muted); }
.info-value  { font-size: .82rem; font-weight: 600; }

.info-note {
    display: flex; align-items: flex-start; gap: 8px;
    margin-top: 14px;
    padding: 11px 13px;
    background: var(--body-bg);
    border-radius: var(--r-sm);
    font-size: .78rem;
    color: var(--muted);
    line-height: 1.5;
}

/* ── Stat grid ── */
.stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.stat-box {
    background: var(--body-bg);
    border-radius: var(--r-sm);
    padding: 14px 10px;
    text-align: center;
    border: 1px solid var(--border);
    transition: transform .15s, box-shadow .15s;
}
.stat-box:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.07); }
.stat-icon { width: 20px; height: 20px; display: block; margin: 0 auto 6px; }
.stat-primary .stat-icon { color: var(--primary); }
.stat-warning .stat-icon { color: #f59e0b; }
.stat-success .stat-icon { color: #10b981; }
.stat-info    .stat-icon { color: #3b82f6; }
.stat-num   { font-size: 1.4rem; font-weight: 700; line-height: 1.1; }
.stat-label { font-size: .7rem; color: var(--muted); margin-top: 3px; }

/* ── Password field ── */
.pw-wrap { position: relative; }
.pw-wrap .finp { padding-right: 40px; }
.pw-eye {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--muted); padding: 0; display: flex; align-items: center;
}
.pw-eye:hover { color: var(--primary); }

.req { color: #ef4444; margin-left: 2px; }

.field-err {
    font-size: .76rem;
    color: #dc2626;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.finp.is-invalid { border-color: #ef4444 !important; }

/* ── Strength meter ── */
.strength-bar {
    height: 4px;
    background: var(--border);
    border-radius: 99px;
    overflow: hidden;
}
.strength-fill {
    height: 100%;
    width: 0;
    border-radius: 99px;
    transition: width .3s ease, background .3s ease;
}
.strength-text { font-size: .72rem; color: var(--muted); margin-top: 3px; height: 14px; }

/* ── Admin row ── */
.admin-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.admin-row:last-of-type { border-bottom: none; }
.admin-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: var(--body-bg);
    border: 2px solid var(--border);
    color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .95rem;
    flex-shrink: 0;
}
.admin-info { flex: 1; min-width: 0; }
.admin-name  { font-weight: 600; font-size: .88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.admin-email { font-size: .76rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.admin-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }

.btn-sm { padding: 5px 10px !important; font-size: .78rem !important; }
/* ── Secondary Button ── */
.btn-sec {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--r-sm);
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s, border-color .15s, color .15s;
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--muted);
    white-space: nowrap;
}
.btn-sec:hover {
    background: var(--body-bg);
    border-color: #9ca3af;
    color: var(--text);
}

.empty-state { padding: 24px; text-align: center; }

/* ── Modal ── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeIn .2s ease;
}
.modal-overlay.open { display: flex; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.modal-box {
    background: var(--card-bg, #fff);
    border-radius: var(--r);
    width: 100%;
    max-width: 460px;
    padding: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    animation: scaleIn .2s ease;
}
@keyframes scaleIn { from { opacity:0; transform:scale(.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }

.modal-hd {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
}
.modal-title { font-weight: 700; font-size: .95rem; display: flex; align-items: center; }
.modal-sub   { font-size: .78rem; color: var(--muted); margin-top: 3px; }
.modal-close {
    background: none; border: none; cursor: pointer;
    color: var(--muted); padding: 2px;
    border-radius: var(--r-sm);
    display: flex; align-items: center;
}
.modal-close:hover { background: var(--body-bg); color: var(--text); }

.modal-warn {
    display: flex; align-items: flex-start; gap: 8px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: var(--r-sm);
    padding: 10px 13px;
    font-size: .79rem;
    color: #92400e;
    margin-bottom: 16px;
    line-height: 1.5;
}
</style>

{{-- ═══════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════ --}}
<script>
/* ── Toggle show/hide password ── */
function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.innerHTML = isText
        ? '<i data-lucide="eye" style="width:15px;height:15px"></i>'
        : '<i data-lucide="eye-off" style="width:15px;height:15px"></i>';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* ── Password strength (untuk form owner) ── */
function checkStrength(val) {
    checkStrengthEl(val, 'strength-fill', 'strength-text');
}

function checkStrengthEl(val, fillId, textId) {
    const fill = document.getElementById(fillId);
    const text = document.getElementById(textId);
    if (!fill || !text) return;

    let score = 0;
    if (val.length >= 8)              score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))    score++;

    const levels = [
        { w: '0%',   bg: '',        label: '' },
        { w: '25%',  bg: '#ef4444', label: '🔴 Sangat Lemah' },
        { w: '50%',  bg: '#f97316', label: '🟠 Lemah' },
        { w: '75%',  bg: '#eab308', label: '🟡 Sedang' },
        { w: '100%', bg: '#22c55e', label: '🟢 Kuat' },
    ];

    const lv = val.length === 0 ? levels[0] : levels[score];
    fill.style.width      = lv.w;
    fill.style.background = lv.bg;
    text.textContent      = lv.label;
}

/* ── Modal helpers ── */
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
    document.body.style.overflow = '';
}

function closeModalOverlay(event, id) {
    // Tutup hanya jika klik overlay (bukan konten di dalam)
    if (event.target.id === id) closeModal(id);
}

/* ── Auto-hide flash setelah 5 detik ── */
document.addEventListener('DOMContentLoaded', function () {
    const flash = document.getElementById('flash-msg');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity .4s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 5000);
    }

    // Re-init lucide icons
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>

@endsection