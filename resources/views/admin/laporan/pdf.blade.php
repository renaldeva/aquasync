<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $laporan->judul }}</title>

    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color:#2d3748;
            margin:30px;
        }

        h1{
            font-size:22px;
            margin:0 0 5px;
        }

        h2{
            font-size:16px;
            margin:25px 0 10px;
            color:#1e293b;
        }

        .subtitle{
            color:#64748b;
            margin-bottom:20px;
        }

        .card{
            border:1px solid #cbd5e1;
            padding:15px;
            border-radius:6px;
        }

        .row{
            margin-bottom:10px;
        }

        .label{
            color:#64748b;
            font-size:11px;
        }

        .value{
            font-size:13px;
            font-weight:bold;
        }

        .badge{
            display:inline-block;
            padding:5px 10px;
            color:#fff;
            border-radius:4px;
            font-size:10px;
        }

        .published{
            background:#16a34a;
        }

        .approved{
            background:#2563eb;
        }

        .default{
            background:#64748b;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        th{
            background:#f1f5f9;
            font-weight:bold;
        }

        th,td{
            border:1px solid #cbd5e1;
            padding:8px;
            text-align:left;
        }

        .divider{
            margin:20px 0;
            border-top:1px solid #e2e8f0;
        }

        .footer{
            margin-top:30px;
            text-align:center;
            font-size:10px;
            color:#94a3b8;
        }

        .alert-good{
            background:#dcfce7;
            border:1px solid #86efac;
            padding:10px;
            border-radius:4px;
        }

        .alert-warning{
            background:#fef3c7;
            border:1px solid #fcd34d;
            padding:10px;
            border-radius:4px;
        }

    </style>
</head>
<body>

@php
    $konten = $laporan->konten ?? [];

    $statusClass = match($laporan->status){
        'published' => 'published',
        'approved' => 'approved',
        default => 'default'
    };
@endphp

<h1>{{ $laporan->judul }}</h1>

<div class="subtitle">
    Laporan Operasional AquaSync
</div>

<span class="badge {{ $statusClass }}">
    {{ strtoupper($laporan->status) }}
</span>

<div class="divider"></div>

<div class="card">

    <div class="row">
        <div class="label">Tipe Laporan</div>
        <div class="value">
            {{ ucfirst(str_replace('_',' ',$laporan->tipe)) }}
        </div>
    </div>

    <div class="row">
        <div class="label">Kolam</div>
        <div class="value">
            {{ $laporan->kolam?->nama_kolam ?? 'Semua Kolam' }}
        </div>
    </div>

    <div class="row">
        <div class="label">Periode</div>
        <div class="value">
            {{ $laporan->periode_mulai->format('d/m/Y') }}
            -
            {{ $laporan->periode_selesai->format('d/m/Y') }}
        </div>
    </div>

    <div class="row">
        <div class="label">Tanggal Dibuat</div>
        <div class="value">
            {{ $laporan->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="row">
        <div class="label">Dibuat Oleh</div>
        <div class="value">
            {{ $laporan->pembuat?->name }}
        </div>
    </div>

</div>

@if(isset($konten['kualitas_air']['summary']))

<h2>Statistik Kualitas Air</h2>

<table>
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Minimum</th>
            <th>Rata-rata</th>
            <th>Maksimum</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>pH Air</td>
            <td>{{ $konten['kualitas_air']['summary']['min_ph'] ?? '-' }}</td>
            <td>{{ $konten['kualitas_air']['summary']['avg_ph'] ?? '-' }}</td>
            <td>{{ $konten['kualitas_air']['summary']['max_ph'] ?? '-' }}</td>
        </tr>

        <tr>
            <td>Kekeruhan</td>
            <td>{{ $konten['kualitas_air']['summary']['min_turbidity'] ?? '-' }}</td>
            <td>{{ $konten['kualitas_air']['summary']['avg_turbidity'] ?? '-' }}</td>
            <td>{{ $konten['kualitas_air']['summary']['max_turbidity'] ?? '-' }}</td>
        </tr>

        <tr>
            <td>Ketinggian Air</td>
            <td>{{ $konten['kualitas_air']['summary']['min_water_level'] ?? '-' }}</td>
            <td>{{ $konten['kualitas_air']['summary']['avg_water_level'] ?? '-' }}</td>
            <td>{{ $konten['kualitas_air']['summary']['max_water_level'] ?? '-' }}</td>
        </tr>

        <tr>
            <td>Total Data Sensor</td>
            <td colspan="3">
                {{ $konten['kualitas_air']['summary']['total_record'] ?? 0 }}
            </td>
        </tr>

    </tbody>
</table>

@endif

@if(isset($konten['kualitas_air']['status_ph']))

<h2>Distribusi Status pH</h2>

<table>
    <thead>
        <tr>
            <th>Status</th>
            <th>Jumlah Data</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Normal</td>
            <td>{{ $konten['kualitas_air']['status_ph']['normal'] ?? 0 }}</td>
        </tr>

        <tr>
            <td>Asam</td>
            <td>{{ $konten['kualitas_air']['status_ph']['asam'] ?? 0 }}</td>
        </tr>

        <tr>
            <td>Basa</td>
            <td>{{ $konten['kualitas_air']['status_ph']['basa'] ?? 0 }}</td>
        </tr>

        <tr>
            <td>Kritis</td>
            <td>{{ $konten['kualitas_air']['status_ph']['kritis'] ?? 0 }}</td>
        </tr>
    </tbody>
</table>

@endif

@if(isset($konten['pakan']))

<h2>Ringkasan Pemberian Pakan</h2>

<table>
    <tr>
        <th>Keterangan</th>
        <th>Nilai</th>
    </tr>

    <tr>
        <td>Total Eksekusi</td>
        <td>{{ $konten['pakan']['total_eksekusi'] ?? 0 }}</td>
    </tr>

    <tr>
        <td>Eksekusi Berhasil</td>
        <td>{{ $konten['pakan']['sukses'] ?? 0 }}</td>
    </tr>
</table>

@endif

@if(isset($konten['panen']))

<h2>Ringkasan Panen</h2>

<table>
    <tr>
        <th>Keterangan</th>
        <th>Nilai</th>
    </tr>

    <tr>
        <td>Total Berat Panen</td>
        <td>{{ $konten['panen']['total_berat'] ?? 0 }} kg</td>
    </tr>

    <tr>
        <td>Total Nilai Panen</td>
        <td>
            Rp {{ number_format($konten['panen']['total_nilai'] ?? 0,0,',','.') }}
        </td>
    </tr>
</table>

@endif

@if(isset($konten['kualitas_air']['summary']))

<h2>Kesimpulan</h2>

@php
    $avgPh = $konten['kualitas_air']['summary']['avg_ph'] ?? 0;
@endphp

@if($avgPh >= 6.5 && $avgPh <= 8.5)

<div class="alert-good">
    Rata-rata pH sebesar <strong>{{ $avgPh }}</strong>.
    Kondisi kualitas air masih berada dalam rentang ideal untuk budidaya ikan lele.
</div>

@else

<div class="alert-warning">
    Rata-rata pH sebesar <strong>{{ $avgPh }}</strong>.
    Kondisi kualitas air berada di luar rentang ideal budidaya ikan lele dan memerlukan perhatian lebih lanjut.
</div>

@endif

@endif

<div class="footer">
    Generated by AquaSync System • {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>