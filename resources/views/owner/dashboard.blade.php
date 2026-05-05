@extends('layouts.owner')
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
                <div class="stat-sub">Kolam dipantau</div>
            </div>
            <div class="stat-ico ico-teal"><i data-lucide="grid-2x2" style="width:22px;height:22px"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Rata-rata pH Air</div>
                <div class="stat-val" style="color:var(--success)">{{ $summary['rata_rata_ph'] ?? '–' }}</div>
                <div class="stat-sub">{{ $summary['rata_rata_ph'] ? 'Dalam batas normal' : 'Belum ada data' }}</div>
            </div>
            <div class="stat-ico ico-green"><i data-lucide="activity" style="width:22px;height:22px"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Kolam Bermasalah</div>
                <div class="stat-val" style="color:{{ $summary['kolam_bermasalah'] > 0 ? 'var(--danger)' : 'var(--success)' }}">
                    {{ $summary['kolam_bermasalah'] }}
                </div>
                <div class="stat-sub">{{ $summary['kolam_bermasalah'] > 0 ? 'Perlu perhatian' : 'Semua normal' }}</div>
            </div>
            <div class="stat-ico ico-orange"><i data-lucide="alert-triangle" style="width:22px;height:22px"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">Flag Saya (Pending)</div>
                <div class="stat-val">{{ $summary['flag_saya'] }}</div>
                <div class="stat-sub">Menunggu balasan admin</div>
            </div>
            <div class="stat-ico ico-red"><i data-lucide="message-square" style="width:22px;height:22px"></i></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Status Kolam --}}
    <div class="col-12 col-xl-8">
        <div class="card-a">
            <div class="card-hd">
                <div class="card-ttl"><span class="live-dot"></span>Status Kolam (Real-time)</div>
                <a href="{{ route('owner.monitoring.kualitas-air') }}" class="sl">Detail →</a>
            </div>
            @if($kolamStatus->count())
            <div style="overflow-x:auto">
                <table class="tbl">
                    <thead>
                        <tr><th>Nama Kolam</th><th>pH Saat Ini</th><th>Turbidity</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($kolamStatus as $k)
                        @php
                            $phCls = match($k['status_ph']){
                                'normal'=>'bdg-normal','asam'=>'bdg-asam',
                                'basa'=>'bdg-basa','kritis'=>'bdg-kritis',default=>'bdg-menunggu'
                            };
                            $stMap = [
                                'aman'            => ['Aman','#2dc653'],
                                'perlu_perhatian' => ['Perlu Perhatian','#f4a261'],
                                'butuh_kuras'     => ['Butuh Kuras','#f4a261'],
                                'kritis'          => ['Kritis','#e63946'],
                                'tidak_ada_data'  => ['–','#6b7c93'],
                            ];
                            [$stLbl,$stClr] = $stMap[$k['status_kolam']] ?? ['–','#6b7c93'];
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600">{{ $k['nama_kolam'] }}</div>
                                <div style="font-size:.71rem;color:var(--muted)">{{ $k['kode_kolam'] }}</div>
                            </td>
                            <td>
                                @if($k['ph_saat_ini'])
                                    <span class="bdg {{ $phCls }}">{{ number_format($k['ph_saat_ini'],1) }} ({{ ucfirst($k['status_ph']) }})</span>
                                @else <span style="font-size:.8rem;color:var(--muted)">–</span> @endif
                            </td>
                            <td>
                                @if($k['status_turbidity'])
                                    <span class="bdg {{ $k['status_turbidity']==='jernih' ? 'bdg-aman' : 'bdg-warning' }}">
                                        {{ ucfirst(str_replace('_',' ',$k['status_turbidity'])) }}
                                    </span>
                                @else <span style="color:var(--muted);font-size:.8rem">–</span> @endif
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;font-weight:600;font-size:.83rem;color:{{ $stClr }}">
                                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $stClr }};flex-shrink:0;display:inline-block"></span>
                                    {{ $stLbl }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('owner.monitoring.kualitas-air.detail',$k['id']) }}" class="btn-s" style="padding:5px 12px;font-size:.75rem">Detail</a>
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
            </div>
            @endif
        </div>
    </div>

    {{-- Flag Saya --}}
    <div class="col-12 col-xl-4">
        <div class="card-a" style="height:100%">
            <div class="card-hd">
                <div class="card-ttl">Flag Saya</div>
                <a href="{{ route('owner.komentar.index') }}" class="sl">Semua →</a>
            </div>
            @forelse($flagSaya as $f)
            <div style="border:1px solid var(--border);border-radius:var(--r-sm);padding:13px;margin-bottom:10px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:.7rem;font-weight:600;color:var(--muted);text-transform:capitalize">
                        {{ str_replace('_',' ',$f->target_type) }}
                    </span>
                    @php $sc=['menunggu'=>'bdg-asam','dibalas'=>'bdg-aktif','selesai'=>'bdg-selesai','ditolak'=>'bdg-kritis'][$f->status]??'' @endphp
                    <span class="bdg {{ $sc }}" style="font-size:.68rem">{{ ucfirst($f->status) }}</span>
                </div>
                <div style="font-size:.82rem;color:var(--muted);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px">
                    "{{ $f->isi_komentar }}"
                </div>
                @if($f->isi_balasan)
                <div style="background:var(--primary-lt);border-radius:6px;padding:8px 10px;font-size:.79rem;color:var(--primary)">
                    <strong>Balasan:</strong> {{ Str::limit($f->isi_balasan, 60) }}
                </div>
                @endif
            </div>
            @empty
            <div class="empty" style="padding:28px 16px">
                <i data-lucide="message-square" style="width:32px;height:32px;display:block;margin:0 auto 8px;opacity:.25"></i>
                <h3>Belum Ada Flag</h3>
                <p style="font-size:.84rem">Kirim flag/komentar ke admin</p>
            </div>
            @endforelse

            <a href="{{ route('owner.komentar.index') }}" class="btn-p" style="width:100%;justify-content:center;margin-top:12px;font-size:.82rem">
                <i data-lucide="plus" style="width:14px;height:14px"></i> Kirim Komentar / Flag
            </a>
        </div>
    </div>
</div>

{{-- pH Chart --}}
<div class="card-a">
    <div class="card-hd">
        <div class="card-ttl">Tren pH Air (24 Jam Terakhir)</div>
        <a href="{{ route('owner.monitoring.kualitas-air') }}" class="sl">Detail Monitoring →</a>
    </div>
    <canvas id="phChart" height="90"></canvas>
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
        const val  = data.length ? data.map(d => d.ph_value) : [7.1,7.2,7.0,6.9,7.3,7.2,7.1,7.0];
        new Chart(document.getElementById('phChart'),{
            type:'line',
            data:{labels:lbl,datasets:[{
                label:'pH Air',data:val,borderColor:'#0e7c7b',
                backgroundColor:'rgba(14,124,123,.07)',borderWidth:2,pointRadius:2.5,tension:.4,fill:true
            },{
                label:'Batas Atas (8.5)',data:new Array(lbl.length).fill(8.5),borderColor:'#e63946',borderWidth:1.5,borderDash:[5,4],pointRadius:0,fill:false
            },{
                label:'Batas Bawah (6.5)',data:new Array(lbl.length).fill(6.5),borderColor:'#f4a261',borderWidth:1.5,borderDash:[5,4],pointRadius:0,fill:false
            }]},
            options:{responsive:true,plugins:{legend:{labels:{font:{size:11}}}},
                scales:{y:{min:5,max:9,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{size:11}}},
                        x:{grid:{display:false},ticks:{font:{size:10},maxTicksLimit:8}}}}
        });
    } catch(e){ console.warn('chart unavailable'); }
});
</script>
@endpush