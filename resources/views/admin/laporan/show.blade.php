@extends('layouts.admin')
@section('title','Detail Laporan')
@section('page-title','Detail Laporan')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div>
        <h1>{{ $laporan->judul }}</h1>
        <p>{{ $laporan->periode_mulai->format('d/m/Y') }} – {{ $laporan->periode_selesai->format('d/m/Y') }}
           · {{ $laporan->kolam?->nama_kolam ?? 'Semua Kolam' }}</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('admin.laporan.index') }}" class="btn-s">
            <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Kembali
        </a>
        <a href="{{ route('admin.laporan.download',$laporan) }}" class="btn-p">
            <i data-lucide="download" style="width:15px;height:15px"></i> Unduh PDF
        </a>
    </div>
</div>

{{-- Info Header --}}
<div class="card-a mb-3">
    <div style="display:flex;gap:24px;flex-wrap:wrap;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px">
        @php $stMap=['draft'=>'bdg-menunggu','published'=>'bdg-aktif','approved'=>'bdg-aman','rejected'=>'bdg-kritis'] @endphp
        @foreach([
            ['Tipe', ucfirst(str_replace('_',' ',$laporan->tipe))],
            ['Periode', $laporan->periode_mulai->format('d/m/Y').' – '.$laporan->periode_selesai->format('d/m/Y')],
            ['Dibuat Oleh', $laporan->pembuat?->name ?? '–'],
            ['Dibuat', $laporan->created_at->format('d/m/Y H:i')],
        ] as [$l,$v])
        <div>
            <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">{{ $l }}</div>
            <div style="font-weight:600;font-size:.88rem">{{ $v }}</div>
        </div>
        @endforeach
        <div>
            <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">Status</div>
            <span class="bdg {{ $stMap[$laporan->status]??'' }}">{{ ucfirst($laporan->status) }}</span>
        </div>
    </div>

    {{-- Konten Laporan --}}
    @if($laporan->konten)
    <h3 style="font-size:.9rem;font-weight:700;margin-bottom:14px">Ringkasan Data</h3>

    @if($laporan->tipe === 'kualitas_air' && isset($laporan->konten['ph_stats']))
    @php $s = $laporan->konten['ph_stats'] @endphp
    <div class="row g-3 mb-3">
        @foreach([['Rata-rata pH',$s['avg']??'–'],['pH Minimum',$s['min']??'–'],['pH Maksimum',$s['max']??'–'],['Total Bacaan',$s['total']??'–']] as [$l,$v])
        <div class="col-6 col-md-3">
            <div style="background:var(--body-bg);border-radius:var(--r-sm);padding:14px;text-align:center">
                <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px">{{ $l }}</div>
                <div style="font-size:1.4rem;font-weight:700">{{ $v }}</div>
            </div>
        </div>
        @endforeach
    </div>

    @elseif($laporan->tipe === 'panen' && isset($laporan->konten['total_berat']))
    <div class="row g-3 mb-3">
        @foreach([['Total Berat',number_format($laporan->konten['total_berat']).' kg'],['Total Nilai','Rp '.number_format($laporan->konten['total_nilai']??0)]] as [$l,$v])
        <div class="col-6">
            <div style="background:var(--body-bg);border-radius:var(--r-sm);padding:14px;text-align:center">
                <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px">{{ $l }}</div>
                <div style="font-size:1.3rem;font-weight:700">{{ $v }}</div>
            </div>
        </div>
        @endforeach
    </div>

    @elseif($laporan->tipe === 'pakan' && isset($laporan->konten['total_eksekusi']))
    <div class="row g-3 mb-3">
        @foreach([['Total Eksekusi',$laporan->konten['total_eksekusi']],['Berhasil',$laporan->konten['sukses']??'–']] as [$l,$v])
        <div class="col-6">
            <div style="background:var(--body-bg);border-radius:var(--r-sm);padding:14px;text-align:center">
                <div style="font-size:.71rem;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px">{{ $l }}</div>
                <div style="font-size:1.3rem;font-weight:700">{{ $v }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Raw JSON --}}
    <details style="margin-top:16px">
        <summary style="font-size:.82rem;font-weight:600;color:var(--muted);cursor:pointer;user-select:none">Lihat data mentah (JSON)</summary>
        <pre style="background:var(--body-bg);border-radius:var(--r-sm);padding:14px;font-size:.77rem;overflow-x:auto;margin-top:10px;font-family:'DM Mono',monospace;line-height:1.5">{{ json_encode($laporan->konten, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    </details>
    @endif
</div>
@endsection