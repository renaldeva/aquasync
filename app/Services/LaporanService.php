<?php

namespace App\Services;
 
use App\Models\{Laporan, KualitasAir, HasilPanen, RiwayatPakan, Pengurasan};
use Carbon\Carbon;
 
class LaporanService
{
    public function generate(array $params): Laporan
    {
        return Laporan::create([
            'judul'           => 'Laporan ' . ucfirst(str_replace('_', ' ', $params['tipe'])) . ' – ' . now()->format('d/m/Y'),
            'tipe'            => $params['tipe'],
            'periode_mulai'   => $params['periode_mulai'],
            'periode_selesai' => $params['periode_selesai'],
            'kolam_id'        => $params['kolam_id'] ?? null,
            'konten'          => $this->buildKonten($params),
            'status'          => 'published',
            'dibuat_oleh'     => auth()->id(),
        ]);
    }
 
    private function buildKonten(array $p): array
    {
    $start   = $p['periode_mulai'];
    $end     = $p['periode_selesai'];
    $kolamId = $p['kolam_id'] ?? null;

    return match ($p['tipe']) {

        'kualitas_air' => $this->buildKualitasAir($start, $end, $kolamId),

        'pakan' => [
            'total_eksekusi' => RiwayatPakan::query()
                ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                ->whereBetween('waktu_eksekusi', [$start, $end])
                ->count(),

            'sukses' => RiwayatPakan::query()
                ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                ->whereBetween('waktu_eksekusi', [$start, $end])
                ->where('status', 'sukses')
                ->count(),
        ],

        'panen' => [
            'total_berat' => HasilPanen::query()
                ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                ->whereBetween('tanggal_panen', [$start, $end])
                ->sum('total_berat'),

            'total_nilai' => HasilPanen::query()
                ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                ->whereBetween('tanggal_panen', [$start, $end])
                ->sum('total_nilai'),

            'jumlah_panen' => HasilPanen::query()
                ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                ->whereBetween('tanggal_panen', [$start, $end])
                ->count(),
        ],

        'bulanan', 'mingguan' => [
            'kualitas_air' => $this->buildKualitasAir($start, $end, $kolamId),

            'pakan' => [
                'total_eksekusi' => RiwayatPakan::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('waktu_eksekusi', [$start, $end])
                    ->count(),

                'sukses' => RiwayatPakan::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('waktu_eksekusi', [$start, $end])
                    ->where('status', 'sukses')
                    ->count(),
            ],

            'panen' => [
                'total_berat' => HasilPanen::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('tanggal_panen', [$start, $end])
                    ->sum('total_berat'),

                'total_nilai' => HasilPanen::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('tanggal_panen', [$start, $end])
                    ->sum('total_nilai'),
            ],

            'generated_at' => now()->toDateTimeString(),
        ],

        default => [
            'generated_at' => now()->toDateTimeString(),
        ],
    };
    }

    private function buildKualitasAir($start, $end, $kolamId): array
    {
    $query = KualitasAir::query()
        ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
        ->whereBetween('recorded_at', [$start, $end]);

    return [

        'summary' => [

            'avg_ph' => round((float)$query->avg('ph_value'), 2),

            'min_ph' => round((float)$query->min('ph_value'), 2),

            'max_ph' => round((float)$query->max('ph_value'), 2),

            'avg_turbidity' => round((float)$query->avg('turbidity_value'), 2),

            'min_turbidity' => round((float)$query->min('turbidity_value'), 2),

            'max_turbidity' => round((float)$query->max('turbidity_value'), 2),

            'avg_water_level' => round((float)$query->avg('water_level'), 2),

            'min_water_level' => round((float)$query->min('water_level'), 2),

            'max_water_level' => round((float)$query->max('water_level'), 2),

            'total_record' => $query->count(),
        ],

        'status_ph' => [
            'normal' => (clone $query)->where('status_ph', 'normal')->count(),
            'asam'   => (clone $query)->where('status_ph', 'asam')->count(),
            'basa'   => (clone $query)->where('status_ph', 'basa')->count(),
            'kritis' => (clone $query)->where('status_ph', 'kritis')->count(),
        ],
    ];
    }
}