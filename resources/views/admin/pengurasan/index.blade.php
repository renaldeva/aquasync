@extends('layouts.admin')
@section('title','Pengurasan Air')
@section('page-title','Manajemen Pengurasan Air')

@section('content')
<div class="ph"><h1>Pengurasan Air</h1><p>Kontrol dan riwayat pengurasan air otomatis via IoT</p></div>

{{-- Kontrol Manual --}}
<div class="card-a mb-3">
    <div class="card-hd"><div class="card-ttl">Kontrol Manual Per Kolam</div></div>
    <div class="row g-2">
        @forelse($kolam as $k)
        @php
            $sedangKuras = \App\Models\Pengurasan::where('kolam_id',$k->id)->where('status','berlangsung')->exists();
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div style="border:1px solid var(--border);border-radius:var(--r-sm);padding:14px;display:flex;align-items:center;justify-content:space-between;gap:10px">
                <div>
                    <div style="font-weight:600;font-size:.88rem">{{ $k->nama_kolam }}</div>
                    <div style="font-size:.71rem;color:var(--muted)">{{ $k->kode_kolam }}</div>
                    @if($sedangKuras)
                    <span class="bdg bdg-warning" style="font-size:.65rem;margin-top:4px">Sedang Kuras</span>
                    @endif
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0">
                    @if(!$sedangKuras)
                    <form action="{{ route('admin.pengurasan.start',$k) }}" method="POST">
                        @csrf
                        <button class="btn-p" style="padding:6px 12px;font-size:.76rem">
                            <i data-lucide="play" style="width:12px;height:12px"></i> Mulai
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.pengurasan.stop',$k) }}" method="POST">
                        @csrf
                        <button class="btn-d" style="padding:6px 12px;font-size:.76rem">
                            <i data-lucide="square" style="width:12px;height:12px"></i> Stop
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><p style="color:var(--muted);font-size:.875rem">Tidak ada kolam aktif.</p></div>
        @endforelse
    </div>
</div>

{{-- Riwayat --}}
<div class="card-a">
    <div class="card-hd">
        <div class="card-ttl">Riwayat Pengurasan Air</div>
        <span style="font-size:.77rem;color:var(--muted)">{{ $pengurasan->total() }} total</span>
    </div>
    @if($pengurasan->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Kolam</th><th>Waktu Mulai</th><th>Waktu Selesai</th><th>Durasi</th><th>Penyebab</th><th>Trigger</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($pengurasan as $p)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $p->kolam?->nama_kolam }}</div>
                        <div style="font-size:.71rem;color:var(--muted)">{{ $p->device_id ?? '–' }}</div>
                    </td>
                    <td style="font-size:.84rem">{{ $p->waktu_mulai->format('d/m/Y H:i') }}</td>
                    <td style="font-size:.84rem">
                        @if($p->waktu_selesai) {{ $p->waktu_selesai->format('d/m/Y H:i') }}
                        @else <span style="color:var(--warning);font-weight:600">Berlangsung</span> @endif
                    </td>
                    <td>{{ $p->durasi_menit ? $p->durasi_menit.' menit' : '–' }}</td>
                    <td style="font-size:.82rem;text-transform:capitalize">{{ $p->penyebab ? str_replace('_',' ',$p->penyebab) : '–' }}</td>
                    <td><span class="bdg bdg-menunggu" style="text-transform:capitalize">{{ $p->trigger_type }}</span></td>
                    <td>
                        @php $sc=['berlangsung'=>'bdg-asam','selesai'=>'bdg-aman','gagal'=>'bdg-kritis'][$p->status]??'' @endphp
                        <span class="bdg {{ $sc }}">{{ ucfirst($p->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $pengurasan->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="droplets" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Riwayat Pengurasan</h3>
        <p>Data muncul otomatis saat IoT melakukan pengurasan</p>
    </div>
    @endif
</div>
@endsection