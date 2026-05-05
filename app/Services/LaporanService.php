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
            'kualitas_air' => [
                'ph_stats' => KualitasAir::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('recorded_at', [$start, $end])
                    ->selectRaw('ROUND(AVG(ph_value)::numeric,2) as avg, ROUND(MIN(ph_value)::numeric,2) as min, ROUND(MAX(ph_value)::numeric,2) as max, COUNT(*) as total')
                    ->first()?->toArray() ?? [],
            ],
            'panen' => [
                'total_berat'  => HasilPanen::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('tanggal_panen', [$start, $end])
                    ->sum('total_berat'),
                'total_nilai'  => HasilPanen::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('tanggal_panen', [$start, $end])
                    ->sum('total_nilai'),
                'data'         => HasilPanen::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('tanggal_panen', [$start, $end])
                    ->with('kolam')->get()->toArray(),
            ],
            'pakan' => [
                'total_eksekusi' => RiwayatPakan::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('waktu_eksekusi', [$start, $end])
                    ->count(),
                'sukses' => RiwayatPakan::query()
                    ->when($kolamId, fn($q) => $q->where('kolam_id', $kolamId))
                    ->whereBetween('waktu_eksekusi', [$start, $end])
                    ->where('status', 'sukses')->count(),
            ],
            default => ['generated_at' => now()->toIso8601String()],
        };
    }
}