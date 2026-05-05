@extends('layouts.owner')
@section('title','Jadwal Panen')
@section('page-title','Monitoring Jadwal Panen')

@section('content')
<div class="ph"><h1>Monitoring Jadwal Panen</h1><p>Lihat jadwal dan hasil panen, berikan feedback jika diperlukan</p></div>

<div class="card-a">
    @if($jadwalPanen->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Kolam</th><th>Tgl Rencana</th><th>Est. Berat</th><th>Est. Jumlah</th><th>Status</th><th>Hasil Panen</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($jadwalPanen as $jp)
                @php
                    $stMap=['pending'=>'bdg-menunggu','approved'=>'bdg-aman','rejected'=>'bdg-kritis','selesai'=>'bdg-aktif','ditunda'=>'bdg-asam'];
                @endphp
                <tr>
                    <td style="font-weight:600">{{ $jp->kolam?->nama_kolam }}</td>
                    <td>{{ $jp->tanggal_rencana->format('d M Y') }}</td>
                    <td>{{ $jp->estimasi_berat ? number_format($jp->estimasi_berat).' kg' : '–' }}</td>
                    <td>{{ $jp->estimasi_jumlah ? number_format($jp->estimasi_jumlah).' ekor' : '–' }}</td>
                    <td><span class="bdg {{ $stMap[$jp->status]??'' }}">{{ ucfirst($jp->status) }}</span></td>
                    <td>
                        @if($jp->hasilPanen)
                            <div style="font-weight:600;font-size:.84rem">{{ number_format($jp->hasilPanen->total_berat) }} kg</div>
                            <div style="font-size:.71rem;color:var(--muted)">{{ $jp->hasilPanen->tanggal_panen->format('d/m/Y') }}</div>
                            @if($jp->hasilPanen->total_nilai)
                            <div style="font-size:.78rem;color:var(--primary);font-weight:600">
                                Rp {{ number_format($jp->hasilPanen->total_nilai) }}
                            </div>
                            @endif
                        @else
                            <span style="color:var(--muted);font-size:.8rem">Belum panen</span>
                        @endif
                    </td>
                    <td>
                        @if($jp->status === 'pending')
                        <button class="btn-flag" style="padding:6px 12px;font-size:.76rem" onclick="openFlag('jadwal_panen',{{ $jp->id }})">
                            <i data-lucide="flag" style="width:12px;height:12px"></i> Flag
                        </button>
                        @else
                        <span style="color:var(--muted);font-size:.8rem">–</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $jadwalPanen->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="package" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Jadwal Panen</h3>
        <p>Jadwal panen diatur oleh Admin</p>
    </div>
    @endif
</div>

@include('owner.components.modal-flag')
@endsection