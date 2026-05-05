@extends('layouts.owner')
@section('title','Detail Komentar')
@section('page-title','Detail Komentar')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Detail Komentar</h1><p>Rincian komentar/flag yang Anda kirimkan</p></div>
    <a href="{{ route('owner.komentar.index') }}" class="btn-s">
        <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Kembali
    </a>
</div>

<div class="card-a">
    {{-- Meta info --}}
    <div style="display:flex;gap:20px;flex-wrap:wrap;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px">
        <div>
            <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">Jenis</div>
            <span style="font-size:.84rem;font-weight:700;padding:3px 10px;border-radius:20px;
                {{ $flag->jenis==='flag' ? 'background:#f8d7da;color:#721c24' : 'background:rgba(14,124,123,.1);color:var(--primary)' }}">
                {{ ucfirst($flag->jenis) }}
            </span>
        </div>
        <div>
            <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">Terkait</div>
            <div style="font-weight:600;font-size:.88rem;text-transform:capitalize">{{ str_replace('_',' ',$flag->target_type) }} #{{ $flag->target_id }}</div>
        </div>
        <div>
            <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">Dikirim</div>
            <div style="font-weight:600;font-size:.88rem">{{ $flag->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div>
            <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">Status</div>
            @php $sc=['menunggu'=>'bdg-asam','dibalas'=>'bdg-dibalas','selesai'=>'bdg-selesai','ditolak'=>'bdg-kritis'][$flag->status]??'bdg-menunggu' @endphp
            <span class="bdg {{ $sc }}">{{ ucfirst($flag->status) }}</span>
        </div>
    </div>

    {{-- Pesan --}}
    <div style="margin-bottom:20px">
        <div style="font-size:.82rem;font-weight:600;color:var(--muted);margin-bottom:8px">Pesan Anda</div>
        <div style="background:var(--body-bg);border-radius:var(--r-sm);padding:16px;font-size:.9rem;line-height:1.6;border-left:3px solid var(--border)">
            {{ $flag->isi_komentar }}
        </div>
    </div>

    {{-- Balasan Admin --}}
    @if($flag->isi_balasan)
    <div>
        <div style="font-size:.82rem;font-weight:600;color:var(--muted);margin-bottom:8px">Balasan Admin</div>
        <div style="background:var(--primary-lt);border-radius:var(--r-sm);padding:16px;font-size:.9rem;line-height:1.6;border-left:3px solid var(--primary)">
            <div style="font-size:.72rem;font-weight:600;color:var(--primary);margin-bottom:8px;display:flex;align-items:center;gap:5px">
                <i data-lucide="user-check" style="width:13px;height:13px"></i>
                {{ $flag->penjawab?->name ?? 'Admin' }} · {{ $flag->dibalas_at?->format('d M Y, H:i') }}
            </div>
            {{ $flag->isi_balasan }}
        </div>
    </div>
    @else
    <div style="padding:20px;border:1.5px dashed var(--border);border-radius:var(--r-sm);text-align:center">
        <i data-lucide="clock" style="width:28px;height:28px;display:block;margin:0 auto 8px;opacity:.3"></i>
        <div style="font-size:.84rem;color:var(--muted);font-weight:500">Menunggu balasan dari Admin</div>
    </div>
    @endif
</div>
@endsection