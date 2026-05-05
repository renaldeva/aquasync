@extends('layouts.admin')
@section('title','Akun')
@section('page-title','Akun Admin')

@section('content')
<div class="ph"><h1>Pengaturan Akun</h1><p>Kelola informasi profil dan keamanan akun Admin</p></div>

<div class="row g-3">
    {{-- Profil --}}
    <div class="col-12 col-lg-6">
        <div class="card-a">
            <div class="card-hd"><div class="card-ttl">Informasi Profil</div></div>
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid var(--border)">
                <div style="width:62px;height:62px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:1.02rem">{{ $user->name }}</div>
                    <div style="font-size:.84rem;color:var(--muted)">{{ $user->email }}</div>
                    <span class="bdg bdg-aktif" style="margin-top:5px">Admin</span>
                </div>
            </div>
            <form action="{{ route('admin.akun.profil') }}" method="POST">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="flbl">Nama Lengkap</label>
                        <input type="text" name="name" class="finp" value="{{ $user->name }}" required>
                    </div>
                    <div class="col-12">
                        <label class="flbl">Email</label>
                        <input type="email" class="finp" value="{{ $user->email }}" disabled style="background:var(--body-bg);cursor:not-allowed;opacity:.7">
                        <span style="font-size:.73rem;color:var(--muted);margin-top:4px;display:block">Email tidak dapat diubah</span>
                    </div>
                    <div class="col-12">
                        <label class="flbl">Nomor HP</label>
                        <input type="text" name="phone" class="finp" value="{{ $user->phone }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-p">
                            <i data-lucide="save" style="width:15px;height:15px"></i> Simpan Profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        {{-- Ganti Password --}}
        <div class="card-a mb-3">
            <div class="card-hd"><div class="card-ttl">Ganti Password</div></div>
            <form action="{{ route('admin.akun.password') }}" method="POST">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="flbl">Password Saat Ini *</label>
                        <input type="password" name="current_password" class="finp" required placeholder="••••••••">
                    </div>
                    <div class="col-12">
                        <label class="flbl">Password Baru *</label>
                        <input type="password" name="password" class="finp" required placeholder="Min. 8 karakter">
                    </div>
                    <div class="col-12">
                        <label class="flbl">Konfirmasi Password Baru *</label>
                        <input type="password" name="password_confirmation" class="finp" required placeholder="Ulangi password baru">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-p">
                            <i data-lucide="lock" style="width:15px;height:15px"></i> Ganti Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Info Sistem --}}
        <div class="card-a">
            <div class="card-hd"><div class="card-ttl">Informasi Sistem</div></div>
            @foreach([
                ['Bergabung Sejak', $user->created_at->format('d M Y')],
                ['Role', 'Admin'],
                ['Status Akun', $user->is_active ? 'Aktif' : 'Nonaktif'],
                ['Terakhir Diperbarui', $user->updated_at->diffForHumans()],
            ] as [$label,$val])
            <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border)">
                <span style="font-size:.84rem;color:var(--muted)">{{ $label }}</span>
                <span style="font-size:.84rem;font-weight:600">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection