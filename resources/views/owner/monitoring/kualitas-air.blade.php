@extends('layouts.owner')
@section('title','Monitoring Kualitas Air')
@section('page-title','Monitoring Kualitas Air')

@section('content')
<div class="ph"><h1>Monitoring Kualitas Air</h1><p>Pantau kondisi pH, turbidity, dan level air setiap kolam</p></div>

<div class="row g-3">
    @forelse($kolam as $k)
    @php $lt = $k->latestKualitasAir; @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card-a" style="height:100%">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <div>
                    <div style="font-weight:700">{{ $k->nama_kolam }}</div>
                    <div style="font-size:.71rem;color:var(--muted)">{{ $k->kode_kolam }}</div>
                </div>
                @php $dotClr = !$lt ? '#ddeaea' : match($lt->status_ph){'normal'=>'#2dc653','kritis'=>'#e63946',default=>'#f4a261'} @endphp
                <div style="width:10px;height:10px;border-radius:50%;background:{{ $dotClr }};box-shadow:0 0 0 3px {{ $dotClr }}30"></div>
            </div>

            <div style="text-align:center;padding:14px 0 10px">
                @php $phClr = !$lt ? 'var(--muted)' : match($lt->status_ph){'normal'=>'var(--success)','kritis'=>'var(--danger)',default=>'var(--warning)'} @endphp
                <div style="font-size:2.8rem;font-weight:700;letter-spacing:-2px;line-height:1;color:{{ $phClr }}">
                    {{ $lt ? number_format($lt->ph_value,1) : '–' }}
                </div>
                <div style="font-size:.79rem;color:var(--muted);margin-top:4px">pH Air</div>
                @if($lt)
                @php $pc=match($lt->status_ph){'normal'=>'bdg-normal','asam'=>'bdg-asam','basa'=>'bdg-basa','kritis'=>'bdg-kritis',default=>''} @endphp
                <span class="bdg {{ $pc }}" style="margin-top:8px">{{ ucfirst($lt->status_ph) }}</span>
                @endif
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-top:12px">
                @foreach([['Turbidity', $lt?->turbidity_value, 'NTU'], ['Level Air', $lt?->water_level, 'cm'], ['Update', $lt ? $lt->recorded_at->format('H:i') : '–', 'WIB']] as [$lbl,$val,$unit])
                <div style="background:var(--body-bg);border-radius:8px;padding:9px;text-align:center">
                    <div style="font-size:.64rem;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;margin-bottom:3px">{{ $lbl }}</div>
                    <div style="font-size:.88rem;font-weight:700">{{ $val !== null ? number_format((float)$val,0) : '–' }}</div>
                    <div style="font-size:.64rem;color:var(--muted)">{{ $unit }}</div>
                </div>
                @endforeach
            </div>

            @if($lt?->status_ph && $lt->status_ph !== 'normal')
            <div style="margin-top:10px;padding:8px 12px;background:#fff3cd;border-radius:8px;font-size:.77rem;color:#856404;display:flex;align-items:center;gap:6px">
                <i data-lucide="alert-triangle" style="width:13px;height:13px;flex-shrink:0"></i>
                pH {{ $lt->status_ph }} – pertimbangkan tindakan
            </div>
            @endif

            <div style="display:flex;gap:8px;margin-top:14px">
                <a href="{{ route('owner.monitoring.kualitas-air.detail',$k->id) }}" class="btn-s" style="flex:1;justify-content:center;font-size:.82rem">
                    <i data-lucide="line-chart" style="width:14px;height:14px"></i> Grafik
                </a>
                <button class="btn-flag" style="padding:8px 14px;font-size:.82rem" onclick="openFlag('kolam',{{ $k->id }})">
                    <i data-lucide="flag" style="width:14px;height:14px"></i> Flag
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card-a"><div class="empty">
            <i data-lucide="activity" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
            <h3>Belum Ada Data Kolam</h3>
        </div></div>
    </div>
    @endforelse
</div>

{{-- Modal Flag --}}
@include('owner.components.modal-flag')
@endsection