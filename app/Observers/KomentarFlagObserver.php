<?php
// =============================================
// app/Observers/KomentarFlagObserver.php
//
// CARA DAFTARKAN di AppServiceProvider::boot():
//
// use App\Models\KomentarFlag;
// use App\Observers\KomentarFlagObserver;
//
// KomentarFlag::observe(KomentarFlagObserver::class);
// =============================================
namespace App\Observers;

use App\Models\{KomentarFlag, Notifikasi, User};
use Illuminate\Support\Str;

class KomentarFlagObserver
{
    /**
     * Saat Owner membuat flag/komentar baru → kirim ke semua Admin
     */
    public function created(KomentarFlag $flag): void
    {
        // Cari nama owner
        $ownerName = $flag->user?->name ?? 'Owner';
        $jenisLabel = ucfirst($flag->jenis);
        $targetLabel = ucfirst(str_replace('_', ' ', $flag->target_type));
        $preview    = '"' . Str::limit($flag->isi_komentar, 80) . '"';

        // Kirim ke semua admin
        User::where('role', 'admin')
            ->where('is_active', true)
            ->get()
            ->each(function ($admin) use ($flag, $ownerName, $jenisLabel, $targetLabel, $preview) {
                Notifikasi::kirim(
                    $admin->id,
                    "{$jenisLabel} baru dari {$ownerName}",
                    "Terkait {$targetLabel}: {$preview}",
                    'flag',
                    $flag->target_type,
                    $flag->target_id
                );
            });
    }

    /**
     * Saat Admin membalas (status → 'dibalas') → kirim ke Owner
     */
    public function updated(KomentarFlag $flag): void
    {
        // Hanya proses jika ada perubahan status atau isi_balasan
        if (!$flag->wasChanged(['status', 'isi_balasan'])) {
            return;
        }

        $jenisLabel = ucfirst($flag->jenis);

        // Admin membalas → notifikasi ke Owner
        if ($flag->wasChanged('isi_balasan') && $flag->isi_balasan) {
            Notifikasi::kirim(
                $flag->user_id,
                "Admin membalas {$jenisLabel} Anda",
                '"' . Str::limit($flag->isi_balasan, 100) . '"',
                'info',
                'komentar_flag',
                $flag->id
            );
        }

        // Status berubah jadi selesai → notifikasi ke Owner
        if ($flag->wasChanged('status') && $flag->status === 'selesai') {
            Notifikasi::kirim(
                $flag->user_id,
                "{$jenisLabel} Anda telah diselesaikan",
                "Admin telah menandai {$jenisLabel} Anda sebagai selesai.",
                'success',
                'komentar_flag',
                $flag->id
            );
        }

        // Status berubah jadi ditolak → notifikasi ke Owner
        if ($flag->wasChanged('status') && $flag->status === 'ditolak') {
            Notifikasi::kirim(
                $flag->user_id,
                "{$jenisLabel} Anda ditolak",
                "Admin menolak {$jenisLabel} Anda.",
                'danger',
                'komentar_flag',
                $flag->id
            );
        }
    }
}