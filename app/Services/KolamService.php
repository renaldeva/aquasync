<?php

namespace App\Services;
 
use App\Models\{Kolam, IotDevice, KualitasAir};
 
class KolamService
{
    public function store(array $data): Kolam
    {
        $lastId = Kolam::max('id') ?? 0;
        $data['kode_kolam'] = 'KLM-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        $data['created_by'] = auth()->id();
        return Kolam::create($data);
    }
 
    public function update(Kolam $kolam, array $data): Kolam
    {
        $kolam->update($data);
        return $kolam->fresh();
    }
 
    public function nonaktifkan(Kolam $kolam): void
    {
        $kolam->update(['status' => 'kosong']);
    }
 
    public function getWithLatestPH(): \Illuminate\Database\Eloquent\Collection
    {
        return Kolam::with('latestKualitasAir', 'iotDevices')
            ->withCount(['jadwalPakan as jadwal_aktif' => fn($q) => $q->where('status', 'aktif')])
            ->latest()
            ->get();
    }
}