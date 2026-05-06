<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #2d3748;
            margin: 30px;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 12px;
            color: #718096;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }

        .row {
            margin-bottom: 12px;
        }

        .label {
            font-size: 10px;
            color: #718096;
            margin-bottom: 2px;
        }

        .value {
            font-size: 13px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            border-radius: 4px;
            color: #fff;
        }

        .published {
            background: #38a169;
        }

        .approved {
            background: #3182ce;
        }

        .default {
            background: #a0aec0;
        }

        .divider {
            margin: 15px 0;
            border-top: 1px solid #e2e8f0;
        }

        .footer {
            margin-top: 25px;
            font-size: 10px;
            color: #a0aec0;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <h1>{{ $laporan->judul }}</h1>
    <div class="subtitle">
        Laporan Operasional
    </div>

    {{-- STATUS BADGE --}}
    @php
        $statusClass = match($laporan->status) {
            'published' => 'published',
            'approved' => 'approved',
            default => 'default'
        };
    @endphp

    <span class="badge {{ $statusClass }}">
        {{ strtoupper($laporan->status) }}
    </span>

    <div class="divider"></div>

    {{-- CARD --}}
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
                {{ $laporan->periode_mulai->format('d/m/Y') }} -
                {{ $laporan->periode_selesai->format('d/m/Y') }}
            </div>
        </div>

        <div class="row">
            <div class="label">Tanggal Dibuat</div>
            <div class="value">
                {{ $laporan->created_at->format('d/m/Y') }}
            </div>
        </div>

        <div class="row">
            <div class="label">Dibuat Oleh</div>
            <div class="value">
                {{ $laporan->pembuat?->name }}
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Generated on {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>