@extends('layouts.owner')
@section('title','Jadwal Pakan')
@section('page-title','Monitoring Jadwal Pakan')

@section('content')
<div class="ph"><h1>Monitoring Jadwal Pakan</h1><p>Lihat jadwal pemberian pakan dan berikan masukan jika diperlukan</p></div>

<div class="card-a">
    @if($jadwalPakan->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Nama Jadwal</th><th>Kolam</th><th>Waktu</th><th>Jumlah</th><th>Jenis Pakan</th><th>Status</th><th>Flag</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($jadwalPakan as $j)
                <tr>
                    <td style="font-weight:600">{{ $j->nama_jadwal ?? 'Jadwal Pakan' }}</td>
                    <td>{{ $j->kolam?->nama_kolam }}</td>
                    <td>
                        <span class="mono" style="font-weight:700;font-size:.94rem">{{ substr($j->waktu_pakan,0,5) }}</span>
                        <span style="font-size:.71rem;color:var(--muted)"> WIB</span>
                    </td>
                    <td>{{ $j->jumlah_pakan ? number_format($j->jumlah_pakan).' '.$j->satuan : '–' }}</td>
                    <td>{{ $j->jenis_pakan ?? '–' }}</td>
                    <td><span class="bdg bdg-aktif">{{ ucfirst($j->status) }}</span></td>
                    <td>
                        @if($j->flag_status !== 'none')
                            <span class="bdg bdg-flagged">{{ ucfirst($j->flag_status) }}</span>
                        @else <span style="color:var(--muted);font-size:.8rem">–</span> @endif
                    </td>
                    <td>
                        <button class="btn-flag" style="padding:6px 12px;font-size:.76rem" onclick="openFlag('jadwal_pakan',{{ $j->id }})">
                            <i data-lucide="flag" style="width:12px;height:12px"></i> Flag
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty">
        <i data-lucide="calendar-clock" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Jadwal Pakan</h3>
        <p>Jadwal pakan diatur oleh Admin</p>
    </div>
    @endif
</div>

@include('owner.components.modal-flag')
@endsection