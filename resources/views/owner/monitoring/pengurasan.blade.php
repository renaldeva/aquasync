@extends('layouts.owner')
@section('title','Pengurasan Air')
@section('page-title','Monitoring Pengurasan Air')

@section('content')
<div class="ph"><h1>Monitoring Pengurasan Air</h1><p>Riwayat pengurasan air otomatis dari sistem IoT</p></div>

<div class="card-a">
    @if($pengurasan->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Kolam</th><th>Waktu Mulai</th><th>Waktu Selesai</th><th>Durasi</th><th>Penyebab</th><th>Trigger</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($pengurasan as $p)
                <tr>
                    <td style="font-weight:600">{{ $p->kolam?->nama_kolam }}</td>
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