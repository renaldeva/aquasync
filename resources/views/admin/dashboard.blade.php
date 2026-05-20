@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Ringkasan Sistem')

@push('styles')
<style>
.pulse-green { animation: pulseGreen 1.8s ease-in-out infinite; }
.pulse-red   { animation: pulseRed   1.8s ease-in-out infinite; }
@keyframes pulseGreen {
    0%,100%{ box-shadow: 0 0 0 0 rgba(45,198,83,.5); }
    50%    { box-shadow: 0 0 0 8px rgba(45,198,83,0); }
}
@keyframes pulseRed {
    0%,100%{ box-shadow: 0 0 0 0 rgba(230,57,70,.4); }
    50%    { box-shadow: 0 0 0 8px rgba(230,57,70,0); }
}
.device-card {
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    padding: 16px;
    transition: border-color .15s;
}
.device-card:hover { border-color: var(--primary); }
.device-card.online  { border-left: 3px solid var(--success); }
.device-card.offline { border-left: 3px solid var(--danger); }
.device-card.warning { border-left: 3px solid var(--warning); }

.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    border-bottom: 1px solid var(--border);
    font-size: .84rem;
}
.metric-row:last-child { border-bottom: none; }
.metric-label { color: var(--muted); }
.metric-value { font-weight: 600; font-family: 'DM Mono', monospace; font-size: .82rem; }
</style>
@endpush

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
<div class="row g-3 mb-4">
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

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SECTION: STATUS IoT (digabung dari iot-status.blade.php)       --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}

{{-- Divider header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
    <div style="display:flex;align-items:center;gap:8px">
        <i data-lucide="wifi" style="width:18px;height:18px;color:var(--primary)"></i>
        <span style="font-weight:700;font-size:.95rem">Status Koneksi IoT</span>
    </div>
    <div style="flex:1;height:1px;background:var(--border)"></div>
    <button class="btn-s" onclick="refreshIoT()" style="font-size:.77rem;padding:5px 12px">
        <i data-lucide="refresh-cw" style="width:13px;height:13px" id="iot-refresh-icon"></i>
        Refresh
    </button>

</div>

{{-- Row: MQTT + Broker + Device summary --}}
<div class="row g-3 mb-3">

    {{-- Status MQTT Subscriber --}}
    <div class="col-12 col-md-4">
        @php
            $lastMqttLog = \App\Models\MqttLog::latest('created_at')->first();
            $mqttAktif   = $lastMqttLog && $lastMqttLog->created_at->diffInMinutes(now()) < 5;
        @endphp
        <div class="card-a">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                <div style="width:42px;height:42px;border-radius:10px;
                     background:{{ $mqttAktif ? 'rgba(45,198,83,.1)' : 'rgba(230,57,70,.1)' }};
                     display:flex;align-items:center;justify-content:center">
                    <i data-lucide="radio" style="width:20px;height:20px;color:{{ $mqttAktif ? 'var(--success)' : 'var(--danger)' }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">MQTT Subscriber</div>
                    <div style="font-size:.75rem;color:var(--muted)">php artisan mqtt:subscribe</div>
                </div>
                <div style="margin-left:auto">
                    <div style="width:12px;height:12px;border-radius:50%;
                         background:{{ $mqttAktif ? 'var(--success)' : 'var(--danger)' }};
                         {{ $mqttAktif ? 'animation:pulseGreen 1.8s ease-in-out infinite' : '' }}">
                    </div>
                </div>
            </div>
            <div class="metric-row">
                <span class="metric-label">Status</span>
                <span class="metric-value" style="color:{{ $mqttAktif ? 'var(--success)' : 'var(--danger)' }}">
                    {{ $mqttAktif ? '✓ Berjalan' : '✗ Tidak Aktif' }}
                </span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Pesan terakhir</span>
                <span class="metric-value">
                    {{ $lastMqttLog ? $lastMqttLog->created_at->diffForHumans() : 'Tidak ada' }}
                </span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Total log hari ini</span>
                <span class="metric-value">
                    {{ \App\Models\MqttLog::whereDate('created_at', today())->count() }} pesan
                </span>
            </div>
            @if(!$mqttAktif)
            <div style="margin-top:12px;padding:10px 12px;background:#fff5f5;border-radius:8px;font-size:.79rem;color:var(--danger)">
                <strong>Jalankan:</strong><br>
                <code style="font-size:.77rem">php artisan mqtt:subscribe</code>
            </div>
            @endif
        </div>
    </div>

    {{-- Status HiveMQ Broker --}}
    <div class="col-12 col-md-4">
        @php
            $lastPing     = \App\Models\MqttLog::where('topic', 'like', '%/ping')
                                ->latest('created_at')->first();
            $brokerOnline = $lastPing && $lastPing->created_at->diffInMinutes(now()) < 5;
        @endphp
        <div class="card-a">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                <div style="width:42px;height:42px;border-radius:10px;
                     background:{{ $brokerOnline ? 'rgba(45,198,83,.1)' : 'rgba(244,162,97,.1)' }};
                     display:flex;align-items:center;justify-content:center">
                    <i data-lucide="cloud" style="width:20px;height:20px;color:{{ $brokerOnline ? 'var(--success)' : 'var(--warning)' }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">HiveMQ Cloud</div>
                    <div style="font-size:.75rem;color:var(--muted)">MQTT Broker</div>
                </div>
                <div style="margin-left:auto">
                    <div style="width:12px;height:12px;border-radius:50%;
                         background:{{ $brokerOnline ? 'var(--success)' : 'var(--warning)' }}">
                    </div>
                </div>
            </div>
            <div class="metric-row">
                <span class="metric-label">Status</span>
                <span class="metric-value" style="color:{{ $brokerOnline ? 'var(--success)' : 'var(--warning)' }}">
                    {{ $brokerOnline ? '✓ Menerima data' : '⚠ Tidak ada data baru' }}
                </span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Host</span>
                <span class="metric-value" style="font-size:.74rem">{{ config('mqtt.host','–') }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Port</span>
                <span class="metric-value">{{ config('mqtt.port', 8883) }} (TLS)</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Ping terakhir</span>
                <span class="metric-value">{{ $lastPing ? $lastPing->created_at->diffForHumans() : '–' }}</span>
            </div>
        </div>
    </div>

    {{-- Ringkasan Device --}}
    <div class="col-12 col-md-4">
        @php
            $totalDevice   = \App\Models\IotDevice::count();
            $onlineDevice  = \App\Models\IotDevice::where('status','online')->count();
            $offlineDevice = $totalDevice - $onlineDevice;
        @endphp
        <div class="card-a">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                <div style="width:42px;height:42px;border-radius:10px;background:rgba(29,111,164,.1);display:flex;align-items:center;justify-content:center">
                    <i data-lucide="cpu" style="width:20px;height:20px;color:var(--primary)"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:.9rem">Perangkat IoT</div>
                    <div style="font-size:.75rem;color:var(--muted)">ESP32 terdaftar</div>
                </div>
            </div>
            <div class="metric-row">
                <span class="metric-label">Total device</span>
                <span class="metric-value">{{ $totalDevice }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Online</span>
                <span class="metric-value" style="color:var(--success)">{{ $onlineDevice }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Offline</span>
                <span class="metric-value" style="color:var(--danger)">{{ $offlineDevice }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Data sensor (1 jam)</span>
                <span class="metric-value">
                    {{ \App\Models\KualitasAir::where('recorded_at','>=',now()->subHour())->count() }} bacaan
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Device per Kolam (ringkas, max 6) --}}
<div class="card-a mb-3">
    <div class="card-hd">
        <div class="card-ttl">Status Per Perangkat ESP32</div>
        <span style="font-size:.78rem;color:var(--muted)" id="iot-last-update">
            Update: {{ now()->format('H:i:s') }}
        </span>
    </div>

    @php
        $devices = \App\Models\IotDevice::with('kolam')->take(6)->get();
    @endphp

    @if($devices->count())
    <div class="row g-3">
        @foreach($devices as $dev)
        @php
            $menit       = $dev->last_ping ? $dev->last_ping->diffInMinutes(now()) : 9999;
            $isOnline    = $menit < 2;
            $isWarning   = $menit >= 2 && $menit < 10;
            $cardClass   = $isOnline ? 'online' : ($isWarning ? 'warning' : 'offline');
            $dotColor    = $isOnline ? 'var(--success)' : ($isWarning ? 'var(--warning)' : 'var(--danger)');
            $statusLabel = $isOnline ? 'Online' : ($isWarning ? 'Tidak Stabil' : 'Offline');
            $latestSensor = $dev->kolam?->latestKualitasAir;
            $sensorOk     = $latestSensor && $latestSensor->recorded_at->diffInMinutes(now()) < 5;
        @endphp
        <div class="col-12 col-md-6 col-xl-4">
            <div class="device-card {{ $cardClass }}">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                    <div>
                        <div style="font-weight:700;font-size:.9rem">{{ $dev->nama_device }}</div>
                        <div style="font-size:.72rem;color:var(--muted);font-family:'DM Mono',monospace">
                            {{ $dev->device_id }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="font-size:.72rem;font-weight:600;color:{{ $dotColor }}">
                            {{ $statusLabel }}
                        </span>
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $dotColor }};
                             {{ $isOnline ? 'animation:pulseGreen 1.8s ease-in-out infinite' : '' }}">
                        </div>
                    </div>
                </div>

                @if($dev->kolam)
                <div style="background:var(--body-bg);border-radius:6px;padding:8px 10px;margin-bottom:10px;font-size:.8rem">
                    <i data-lucide="grid-2x2" style="width:12px;height:12px;color:var(--muted);margin-right:4px"></i>
                    {{ $dev->kolam->nama_kolam }} · {{ $dev->kolam->kode_kolam }}
                </div>
                @endif

                <div class="metric-row">
                    <span class="metric-label">Last Ping</span>
                    <span class="metric-value" style="color:{{ $isOnline ? 'var(--success)' : 'var(--danger)' }}">
                        {{ $dev->last_ping ? $dev->last_ping->diffForHumans() : 'Belum pernah' }}
                    </span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">IP Address</span>
                    <span class="metric-value">{{ $dev->ip_address ?? '–' }}</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Firmware</span>
                    <span class="metric-value">{{ $dev->firmware_version ?? '–' }}</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Data sensor</span>
                    <span class="metric-value" style="color:{{ $sensorOk ? 'var(--success)' : 'var(--muted)' }}">
                        @if($latestSensor)
                            pH={{ number_format($latestSensor->ph_value,1) }}
                            · {{ $latestSensor->recorded_at->diffForHumans() }}
                        @else
                            Belum ada data
                        @endif
                    </span>
                </div>

                @if($isOnline && $dev->kolam)
                <div style="margin-top:12px;display:flex;gap:6px">
                    <button class="btn-s" style="flex:1;justify-content:center;padding:7px;font-size:.77rem"
                            onclick="testPing('{{ $dev->device_id }}')">
                        <i data-lucide="wifi" style="width:13px;height:13px"></i> Test Ping
                    </button>
                    <a href="{{ route('admin.kualitas-air.show', $dev->kolam->id) }}"
                       class="btn-p" style="flex:1;justify-content:center;padding:7px;font-size:.77rem;text-decoration:none">
                        <i data-lucide="activity" style="width:13px;height:13px"></i> Grafik pH
                    </a>
                </div>
                @endif

                @if(!$isOnline)
                <div style="margin-top:10px;padding:8px 10px;background:#fff5f5;border-radius:6px;font-size:.77rem;color:var(--danger)">
                    Periksa: power ESP32, koneksi WiFi, dan MQTT credentials
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty">
        <i data-lucide="cpu" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Device Terdaftar</h3>
        <p>Device akan terdaftar otomatis saat ESP32 pertama kali kirim ping ke MQTT</p>
    </div>
    @endif
</div>

{{-- Log MQTT ringkas (10 terakhir) --}}
<div class="card-a">
    <div class="card-hd">
        <div class="card-ttl">Log MQTT Terbaru</div>
        <span style="font-size:.78rem;color:var(--muted)">10 pesan terakhir</span>
    </div>
    @php
        $logsRingkas = \App\Models\MqttLog::latest('created_at')->take(10)->get();
    @endphp
    @if($logsRingkas->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Topic</th>
                    <th>Arah</th>
                    <th>Device</th>
                    <th>Payload</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logsRingkas as $log)
                <tr>
                    <td style="font-family:'DM Mono',monospace;font-size:.77rem;white-space:nowrap">
                        {{ $log->created_at->format('H:i:s') }}
                        <div style="font-size:.68rem;color:var(--muted)">{{ $log->created_at->diffForHumans() }}</div>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:.77rem;max-width:200px">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $log->topic }}
                        </div>
                    </td>
                    <td>
                        @if($log->direction === 'incoming')
                        <span class="bdg bdg-aman" style="font-size:.68rem">↓ Masuk</span>
                        @else
                        <span class="bdg bdg-info" style="font-size:.68rem">↑ Keluar</span>
                        @endif
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:.77rem">
                        {{ $log->device_id ?? '–' }}
                    </td>
                    <td style="max-width:220px">
                        @if($log->payload)
                        <div style="font-family:'DM Mono',monospace;font-size:.72rem;color:var(--muted);
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                             title="{{ json_encode($log->payload) }}">
                            {{ json_encode($log->payload) }}
                        </div>
                        @else
                        <span style="color:var(--muted);font-size:.8rem">–</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty" style="padding:28px">
        <i data-lucide="inbox" style="width:36px;height:36px;display:block;margin:0 auto 10px;opacity:.25"></i>
        <h3>Belum Ada Log MQTT</h3>
        <p>Log akan muncul saat ESP32 mengirim data dan <code>mqtt:subscribe</code> berjalan</p>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// ── Chart pH ────────────────────────────────────────────────────
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

// ── IoT auto refresh tiap 10 detik (status saja, tanpa reload) ──
setInterval(async () => {
    try {
        await fetch('/api/iot/status');
        const el = document.getElementById('iot-last-update');
        if (el) el.textContent = 'Update: ' + new Date().toLocaleTimeString('id');
    } catch(e) {}
}, 10000);

// ── Refresh manual IoT (reload halaman) ─────────────────────────
function refreshIoT() {
    const icon = document.getElementById('iot-refresh-icon');
    if (icon) {
        icon.style.animation = 'spin .5s linear infinite';
        icon.style.transformOrigin = 'center';
    }
    setTimeout(() => window.location.reload(), 300);
}

// ── Test ping ke device ─────────────────────────────────────────
async function testPing(deviceId) {
    try {
        const res = await fetch('/api/iot/device/ping', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ device_id: deviceId })
        });
        const data = await res.json();
        alert(data.success
            ? `✓ Ping berhasil dikirim ke ${deviceId}\nServer time: ${data.server_time}`
            : `✗ Gagal kirim ping ke ${deviceId}`
        );
    } catch(e) {
        alert('Error: ' + e.message);
    }
}

// Spin animation untuk tombol refresh
const _iotStyle = document.createElement('style');
_iotStyle.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(_iotStyle);
</script>
@endpush