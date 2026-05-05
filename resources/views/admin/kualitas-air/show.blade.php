@extends('layouts.admin')
@section('title','Riwayat pH – '.$kolam->nama_kolam)
@section('page-title','Detail Kualitas Air')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div>
        <h1>{{ $kolam->nama_kolam }}</h1>
        <p>{{ $kolam->kode_kolam }} · Riwayat {{ $hours }} jam terakhir</p>
    </div>
    <a href="{{ route('admin.kualitas-air.index') }}" class="btn-s">
        <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Kembali
    </a>
</div>

{{-- Filter --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
    @foreach([1,6,12,24,48,72] as $h)
    <a href="{{ route('admin.kualitas-air.show',$kolam->id) }}?hours={{ $h }}"
       class="{{ $hours==$h ? 'btn-p' : 'btn-s' }}" style="padding:7px 16px;font-size:.81rem">{{ $h }}j</a>
    @endforeach
</div>

{{-- Stats --}}
@if($data->count())
<div class="row g-3 mb-3">
    @php
        $avg  = round($data->avg('ph_value'),2);
        $min  = round($data->min('ph_value'),2);
        $max  = round($data->max('ph_value'),2);
        $abn  = $data->whereIn('status_ph',['asam','basa','kritis'])->count();
    @endphp
    @foreach([['Rata-rata pH',$avg,'var(--text)','minus'],['pH Minimum',$min,'var(--warning)','trending-down'],['pH Maksimum',$max,'var(--primary)','trending-up'],['Bacaan Abnormal',$abn,'var(--danger)','alert-triangle']] as [$l,$v,$c,$ic])
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-lbl">{{ $l }}</div>
                <div class="stat-val" style="font-size:1.5rem;color:{{ $c }}">{{ $v }}</div>
            </div>
            <div class="stat-ico ico-blue"><i data-lucide="{{ $ic }}" style="width:18px;height:18px"></i></div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Chart --}}
<div class="card-a mb-3">
    <div class="card-hd">
        <div class="card-ttl">Grafik pH Air</div>
        <span style="font-size:.77rem;color:var(--muted)">{{ $data->count() }} data point</span>
    </div>
    <canvas id="phChart" height="80"></canvas>
</div>

{{-- Tabel --}}
<div class="card-a">
    <div class="card-hd"><div class="card-ttl">Data Detail</div></div>
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Waktu</th><th>pH</th><th>Status pH</th><th>Turbidity (NTU)</th><th>Level Air (cm)</th><th>Device</th></tr>
            </thead>
            <tbody>
                @forelse($data->sortByDesc('recorded_at') as $d)
                <tr>
                    <td class="mono" style="font-size:.79rem">{{ $d->recorded_at->format('d/m/Y H:i:s') }}</td>
                    <td style="font-weight:700" class="mono">{{ number_format($d->ph_value,2) }}</td>
                    <td>
                        @php $c=match($d->status_ph){'normal'=>'bdg-normal','asam'=>'bdg-asam','basa'=>'bdg-basa','kritis'=>'bdg-kritis',default=>''} @endphp
                        <span class="bdg {{ $c }}">{{ ucfirst($d->status_ph) }}</span>
                    </td>
                    <td>{{ $d->turbidity_value !== null ? number_format($d->turbidity_value,1) : '–' }}</td>
                    <td>{{ $d->water_level !== null ? number_format($d->water_level,1) : '–' }}</td>
                    <td style="font-size:.77rem;color:var(--muted)">{{ $d->device_id ?? '–' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:28px;color:var(--muted)">Belum ada data untuk periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const labels = @json($data->sortBy('recorded_at')->pluck('recorded_at')->map(fn($t)=>\Carbon\Carbon::parse($t)->format('H:i')));
const values = @json($data->sortBy('recorded_at')->pluck('ph_value'));
new Chart(document.getElementById('phChart'),{
    type:'line',
    data:{labels,datasets:[
        {label:'pH Air',data:values,borderColor:'#1d6fa4',backgroundColor:'rgba(29,111,164,.07)',borderWidth:2.5,pointRadius:2,tension:.4,fill:true},
        {label:'Batas Atas (8.5)',data:new Array(labels.length).fill(8.5),borderColor:'#e63946',borderWidth:1.5,borderDash:[5,4],pointRadius:0,fill:false},
        {label:'Batas Bawah (6.5)',data:new Array(labels.length).fill(6.5),borderColor:'#f4a261',borderWidth:1.5,borderDash:[5,4],pointRadius:0,fill:false}
    ]},
    options:{responsive:true,plugins:{legend:{labels:{font:{size:11}}}},
        scales:{y:{min:4,max:10,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{size:11}}},
                x:{grid:{display:false},ticks:{font:{size:10},maxTicksLimit:12}}}}
});
</script>
@endpush