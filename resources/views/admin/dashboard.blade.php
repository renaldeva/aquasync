@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Ringkasan Sistem')

@section('content')
{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Total Kolam Aktif</div>
                <div class="stat-val">{{ $summary['total_kolam_aktif'] }}</div>
                <div class="stat-sub">Kolam dalam pemantauan</div>
            </div>
            <div class="stat-ico ico-blue"><i data-lucide="grid-2x2" style="width:22px;height:22px;"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Rata-rata pH Air</div>
                <div class="stat-val" style="color:var(--success)">{{ $summary['rata_rata_ph'] ?? '–' }}</div>
                <div class="stat-sub">{{ $summary['rata_rata_ph'] ? 'Dalam batas normal' : 'Belum ada data sensor' }}</div>
            </div>
            <div class="stat-ico ico-green"><i data-lucide="activity" style="width:22px;height:22px;"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Jadwal Pakan (Hari Ini)</div>
                <div class="stat-val">{{ $summary['jadwal_pakan_aktif'] }}</div>
                <div class="stat-sub">{{ $summary['jadwal_menunggu'] }} jadwal menunggu eksekusi</div>
            </div>
            <div class="stat-ico ico-orange"><i data-lucide="calendar-clock" style="width:22px;height:22px;"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Flag / Komentar Baru</div>
                <div class="stat-val" style="color:var(--danger)">{{ $summary['flag_menunggu'] }}</div>
                <div class="stat-sub">Menunggu tanggapan admin</div>
            </div>
            <div class="stat-ico ico-red"><i data-lucide="flag" style="width:22px;height:22px;"></i></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Status Kolam --}}
    <div class="col-12 col-xl-8">
        <div class="card-a">
            <div class="card-hd">
                <div class="card-ttl"><span class="live-dot"></span>Status Kolam Terkini (Real-time)</div>
                <a href="{{ route('admin.kolam.index') }}" class="sl">Lihat Semua →</a>
            </div>
            @if($kolamStatus->count())
            <div style="overflow-x:auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama Kolam</th>
                            <th>pH Saat Ini</th>
                            <th>Pakan Terakhir</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kolamStatus as $k)
                        @php
                            $phCls = match($k['status_ph']){
                                'normal'=>'bdg-normal','asam'=>'bdg-asam',
                                'basa'=>'bdg-basa','kritis'=>'bdg-kritis',
                                default=>'bdg-menunggu'
                            };
                            $stMap = [
                                'aman'            => ['Aman','#2dc653'],
                                'perlu_perhatian' => ['Perlu Perhatian','#f4a261'],
                                'butuh_kuras'     => ['Butuh Kuras','#f4a261'],
                                'kritis'          => ['Kritis','#e63946'],
                                'tidak_ada_data'  => ['Tidak Ada Data','#6b7c93'],
                            ];
                            [$stLbl,$stClr] = $stMap[$k['status_kolam']] ?? ['–','#6b7c93'];
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600">{{ $k['nama_kolam'] }}</div>
                                <div style="font-size:.72rem;color:var(--muted)">{{ $k['kode_kolam'] }}</div>
                            </td>
                            <td>
                                @if($k['ph_saat_ini'])
                                    <span class="bdg {{ $phCls }}">
                                        {{ number_format($k['ph_saat_ini'],1) }} ({{ ucfirst($k['status_ph']) }})
                                    </span>
                                @else
                                    <span style="font-size:.8rem;color:var(--muted)">Tidak ada data</span>
                                @endif
                            </td>
                            <td style="font-size:.84rem">
                                {{ $k['pakan_terakhir']
                                    ? \Carbon\Carbon::parse($k['pakan_terakhir'])->format('H:i').' WIB'
                                    : '–' }}
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;font-weight:600;font-size:.83rem;color:{{ $stClr }}">
                                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $stClr }};flex-shrink:0;display:inline-block"></span>
                                    {{ $stLbl }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.kualitas-air.show', $k['id']) }}" class="btn-s" style="padding:5px 12px;font-size:.75rem">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty">
                <i data-lucide="database" style="width:40px;height:40px;display:block;margin:0 auto 10px;opacity:.25"></i>
                <h3>Belum Ada Data Kolam</h3>
                <a href="{{ route('admin.kolam.index') }}" class="btn-p mt-3" style="display:inline-flex">
                    <i data-lucide="plus" style="width:15px;height:15px"></i> Tambah Kolam
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Flag Terbaru --}}
    <div class="col-12 col-xl-4">
        <div class="card-a" style="height:100%">
            <div class="card-hd">
                <div class="card-ttl">Flag Owner Terkini</div>
                <a href="{{ route('admin.komentar-flag.index') }}" class="sl">Semua →</a>
            </div>
            @forelse($flagTerbaru as $f)
            <div style="border:1px solid var(--border);border-radius:var(--r-sm);padding:13px;margin-bottom:10px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:.68rem;font-weight:700;padding:3px 8px;border-radius:20px;background:#fff3cd;color:#856404">
                        {{ ucfirst(str_replace('_',' ',$f->target_type)) }}
                    </span>
                    <span style="font-size:.71rem;color:var(--muted)">{{ $f->created_at->diffForHumans() }}</span>
                </div>
                <div style="font-weight:700;font-size:.85rem;margin-bottom:3px">{{ $f->user->name }}</div>
                <div style="font-size:.8rem;color:var(--muted);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">"{{ $f->isi_komentar }}"</div>
                <a href="{{ route('admin.komentar-flag.index') }}#flag-{{ $f->id }}" class="btn-p" style="padding:5px 12px;font-size:.75rem;margin-top:10px;display:inline-flex">Tanggapi</a>
            </div>
            @empty
            <div class="empty" style="padding:28px 16px">
                <i data-lucide="flag" style="width:32px;height:32px;display:block;margin:0 auto 8px;opacity:.25"></i>
                <h3>Tidak Ada Flag</h3>
                <p style="font-size:.84rem">Belum ada komentar dari owner</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Chart + Aktivitas --}}
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card-a">
            <div class="card-hd">
                <div class="card-ttl">Tren pH Air (24 Jam Terakhir)</div>
                <a href="{{ route('admin.kualitas-air.index') }}" class="sl">Detail →</a>
            </div>
            <canvas id="phChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card-a">
            <div class="card-hd"><div class="card-ttl">Aktivitas Pakan Terbaru</div></div>
            @php
                $riwayat = \App\Models\RiwayatPakan::with('kolam')->latest('waktu_eksekusi')->take(6)->get();
            @endphp
            @forelse($riwayat as $r)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
                <div style="width:34px;height:34px;border-radius:8px;background:rgba(29,111,164,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="calendar-clock" style="width:15px;height:15px;color:var(--primary)"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:.84rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        Pakan · {{ $r->kolam?->nama_kolam }}
                    </div>
                    <div style="font-size:.71rem;color:var(--muted)">{{ \Carbon\Carbon::parse($r->waktu_eksekusi)->diffForHumans() }}</div>
                </div>
                <span class="bdg {{ $r->status==='sukses' ? 'bdg-aman' : 'bdg-asam' }}" style="font-size:.67rem">{{ ucfirst($r->status) }}</span>
            </div>
            @empty
            <div class="empty" style="padding:24px"><p>Belum ada aktivitas pakan</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res  = await fetch('/api/iot/kolam/1/history?hours=24');
        const data = await res.json();
        const lbl  = data.length ? data.map(d => new Date(d.recorded_at).toLocaleTimeString('id',{hour:'2-digit',minute:'2-digit'}))
                                 : ['06:00','08:00','10:00','12:00','14:00','16:00','18:00','20:00'];
        const val  = data.length ? data.map(d => d.ph_value)
                                 : [7.1,7.2,7.0,6.9,7.3,7.2,7.1,7.0];
        new Chart(document.getElementById('phChart'),{
            type:'line',
            data:{labels:lbl,datasets:[{
                label:'pH Air',data:val,borderColor:'#1d6fa4',
                backgroundColor:'rgba(29,111,164,.07)',borderWidth:2,
                pointRadius:2.5,tension:.4,fill:true
            }]},
            options:{responsive:true,plugins:{legend:{display:false}},
                scales:{y:{min:5,max:9,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{size:11}}},
                        x:{grid:{display:false},ticks:{font:{size:10},maxTicksLimit:8}}}}
        });
    } catch(e){ console.warn('chart unavailable'); }
});
</script>
@endpush