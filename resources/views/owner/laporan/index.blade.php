@extends('layouts.owner')
@section('title','Laporan')
@section('page-title','Laporan Operasional')

@section('content')
<div class="ph"><h1>Laporan Operasional</h1><p>Lihat dan unduh laporan yang diterbitkan Admin</p></div>

<div class="card-a">
    @if($laporan->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Judul</th><th>Tipe</th><th>Periode</th><th>Kolam</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($laporan as $l)
                @php $stMap=['published'=>'bdg-aktif','approved'=>'bdg-aman'] @endphp
                <tr>
                    <td style="font-weight:600;max-width:200px">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $l->judul }}</div>
                    </td>
                    <td><span class="bdg bdg-info">{{ ucfirst(str_replace('_',' ',$l->tipe)) }}</span></td>
                    <td style="font-size:.81rem">
                        {{ $l->periode_mulai->format('d/m/Y') }} –<br>{{ $l->periode_selesai->format('d/m/Y') }}
                    </td>
                    <td style="font-size:.84rem">{{ $l->kolam?->nama_kolam ?? 'Semua Kolam' }}</td>
                    <td><span class="bdg {{ $stMap[$l->status]??'bdg-menunggu' }}">{{ ucfirst($l->status) }}</span></td>
                    <td style="font-size:.81rem">
                        <div>{{ $l->created_at->format('d/m/Y') }}</div>
                        <div style="color:var(--muted);font-size:.71rem">{{ $l->pembuat?->name }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px">
                            <a href="{{ route('owner.laporan.show',$l) }}" class="btn-s" style="padding:5px 10px;font-size:.74rem">
                                <i data-lucide="eye" style="width:12px;height:12px"></i>
                            </a>
                            <a href="{{ route('owner.laporan.download',$l) }}" class="btn-p" style="padding:5px 10px;font-size:.74rem">
                                <i data-lucide="download" style="width:12px;height:12px"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $laporan->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="bar-chart-2" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Laporan</h3>
        <p>Laporan akan muncul setelah Admin menerbitkannya</p>
    </div>
    @endif
</div>
@endsection