@extends('layouts.owner')
@section('title','Profil Saya')
@section('page-title','Profil Saya')

@section('content')
<div class="ph"><h1>Profil Saya</h1><p>Informasi akun Owner CV Ranu Slawu</p></div>

<div class="row g-3">
    {{-- Info Profil (read-only untuk owner) --}}
    <div class="col-12 col-lg-6">
        <div class="card-a">
            <div class="card-hd"><div class="card-ttl">Informasi Akun</div></div>
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid var(--border)">
                <div style="width:62px;height:62px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:1.02rem">{{ $user->name }}</div>
                    <div style="font-size:.84rem;color:var(--muted)">{{ $user->email }}</div>
                    <span class="bdg bdg-selesai" style="margin-top:5px">Owner</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:2px">
                @foreach([
                    ['Email', $user->email],
                    ['No. HP', $user->phone ?? '–'],
                    ['Role', 'Owner / Pemilik'],
                    ['Status', $user->is_active ? 'Aktif' : 'Nonaktif'],
                    ['Bergabung', $user->created_at->format('d M Y')],
                ] as [$l,$v])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
                    <span style="font-size:.84rem;color:var(--muted)">{{ $l }}</span>
                    <span style="font-size:.84rem;font-weight:600">{{ $v }}</span>
                </div>
                @endforeach
            </div>

            <div style="margin-top:14px;padding:12px 14px;background:var(--body-bg);border-radius:var(--r-sm);font-size:.8rem;color:var(--muted);display:flex;align-items:center;gap:8px">
                <i data-lucide="info" style="width:14px;height:14px;flex-shrink:0"></i>
                Untuk mengubah nama atau email, hubungi Admin sistem.
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="col-12 col-lg-6">
        <div class="card-a">
            <div class="card-hd"><div class="card-ttl">Ganti Password</div></div>
            <form action="{{ route('owner.akun.password') }}" method="POST">
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

        {{-- Statistik Aktivitas Owner --}}
        <div class="card-a mt-3">
            <div class="card-hd"><div class="card-ttl">Aktivitas Saya</div></div>
            @php
                $totalFlag    = \App\Models\KomentarFlag::where('user_id',auth()->id())->count();
                $flagPending  = \App\Models\KomentarFlag::where('user_id',auth()->id())->where('status','menunggu')->count();
                $flagDibalas  = \App\Models\KomentarFlag::where('user_id',auth()->id())->where('status','dibalas')->count();
                $totalNotif   = \App\Models\Notifikasi::where('user_id',auth()->id())->count();
            @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                @foreach([['Total Flag/Komentar',$totalFlag,'message-square'],['Menunggu Balasan',$flagPending,'clock'],['Sudah Dibalas',$flagDibalas,'check-circle'],['Total Notifikasi',$totalNotif,'bell']] as [$l,$v,$ic])
                <div style="background:var(--body-bg);border-radius:var(--r-sm);padding:14px;text-align:center">
                    <i data-lucide="{{ $ic }}" style="width:20px;height:20px;color:var(--primary);display:block;margin:0 auto 6px"></i>
                    <div style="font-size:1.3rem;font-weight:700">{{ $v }}</div>
                    <div style="font-size:.72rem;color:var(--muted)">{{ $l }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection