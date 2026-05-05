<?php

namespace App\Services;
 
use App\Models\{Kolam, KualitasAir, JadwalPakan, KomentarFlag, Notifikasi, RiwayatPakan, Pengurasan};
use Illuminate\Support\Facades\DB;
 
class DashboardService
{
    public function getAdminSummary(): array
    {
        return [
            'total_kolam_aktif'  => Kolam::where('status', 'aktif')->count(),
            'rata_rata_ph'       => $this->getRataRataPH(),
            'jadwal_pakan_aktif' => JadwalPakan::where('status', 'aktif')->count(),
            'jadwal_menunggu'    => JadwalPakan::where('status', 'aktif')
                                        ->whereTime('waktu_pakan', '>', now()->toTimeString())->count(),
            'flag_menunggu'      => KomentarFlag::where('status', 'menunggu')->count(),
            'notif_unread'       => Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count(),
        ];
    }
 
    public function getOwnerSummary(): array
    {
        return [
            'total_kolam_aktif' => Kolam::where('status', 'aktif')->count(),
            'rata_rata_ph'      => $this->getRataRataPH(),
            'kolam_bermasalah'  => $this->getKolamBermasalah(),
            'flag_saya'         => KomentarFlag::where('user_id', auth()->id())->where('status','menunggu')->count(),
            'notif_unread'      => Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count(),
        ];
    }
 
    public function getKolamStatusList(): \Illuminate\Support\Collection
    {
        return Kolam::with('latestKualitasAir')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($kolam) {
                $latest = $kolam->latestKualitasAir;
                return [
                    'id'             => $kolam->id,
                    'nama_kolam'     => $kolam->nama_kolam,
                    'kode_kolam'     => $kolam->kode_kolam,
                    'ph_saat_ini'    => $latest?->ph_value,
                    'status_ph'      => $latest?->status_ph ?? 'tidak_ada_data',
                    'status_turbidity' => $latest?->status_turbidity,
                    'pakan_terakhir' => RiwayatPakan::where('kolam_id', $kolam->id)
                                            ->latest('waktu_eksekusi')->value('waktu_eksekusi'),
                    'status_kolam'   => $this->resolveKolamStatus($latest),
                ];
            });
    }
 
    public function resolveKolamStatus($latest): string
    {
        if (!$latest) return 'tidak_ada_data';
        if ($latest->status_ph === 'kritis') return 'kritis';
        if ($latest->status_turbidity === 'sangat_keruh') return 'butuh_kuras';
        if (in_array($latest->status_ph, ['asam', 'basa'])) return 'perlu_perhatian';
        return 'aman';
    }
 
    private function getRataRataPH(): ?float
    {
        $result = DB::select("
            SELECT ROUND(AVG(ph_value)::numeric, 1) as avg_ph
            FROM (SELECT DISTINCT ON (kolam_id) ph_value
                  FROM kualitas_air ORDER BY kolam_id, recorded_at DESC) t
        ");
        return $result[0]->avg_ph ?? null;
    }
 
    private function getKolamBermasalah(): int
    {
        return Kolam::with('latestKualitasAir')
            ->where('status', 'aktif')
            ->get()
            ->filter(fn($k) => in_array($k->latestKualitasAir?->status_ph, ['asam','basa','kritis']))
            ->count();
    }
}