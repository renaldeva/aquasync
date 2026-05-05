@extends('layouts.admin')
@section('title','Laporan')
@section('page-title','Manajemen Laporan')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Laporan Operasional</h1><p>Buat, lihat, dan unduh laporan sistem AquaSync</p></div>
    <button class="btn-p" data-bs-toggle="modal" data-bs-target="#mBuat">
        <i data-lucide="plus" style="width:15px;height:15px"></i> Buat Laporan
    </button>
</div>

<div class="card-a">
    @if($laporan->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Judul</th><th>Tipe</th><th>Periode</th><th>Kolam</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($laporan as $l)
                @php $stMap=['draft'=>'bdg-menunggu','published'=>'bdg-aktif','approved'=>'bdg-aman','rejected'=>'bdg-kritis'] @endphp
                <tr>
                    <td style="font-weight:600;max-width:220px">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $l->judul }}</div>
                    </td>
                    <td><span class="bdg bdg-info">{{ ucfirst(str_replace('_',' ',$l->tipe)) }}</span></td>
                    <td style="font-size:.81rem">
                        {{ $l->periode_mulai->format('d/m/Y') }} –<br>{{ $l->periode_selesai->format('d/m/Y') }}
                    </td>
                    <td style="font-size:.84rem">{{ $l->kolam?->nama_kolam ?? 'Semua Kolam' }}</td>
                    <td><span class="bdg {{ $stMap[$l->status]??'' }}">{{ ucfirst($l->status) }}</span></td>
                    <td style="font-size:.81rem">
                        <div>{{ $l->created_at->format('d/m/Y') }}</div>
                        <div style="color:var(--muted);font-size:.72rem">{{ $l->pembuat?->name }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px">
                            <a href="{{ route('admin.laporan.show',$l) }}" class="btn-s" style="padding:5px 10px;font-size:.74rem">
                                <i data-lucide="eye" style="width:12px;height:12px"></i>
                            </a>
                            <a href="{{ route('admin.laporan.download',$l) }}" class="btn-p" style="padding:5px 10px;font-size:.74rem">
                                <i data-lucide="download" style="width:12px;height:12px"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $laporan->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="bar-chart-2" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Laporan</h3>
        <p>Buat laporan operasional kolam lele</p>
        <button class="btn-p mt-3" data-bs-toggle="modal" data-bs-target="#mBuat">Buat Laporan Pertama</button>
    </div>
    @endif
</div>

{{-- Modal Buat Laporan --}}
<div class="modal fade modal-a" id="mBuat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Laporan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.laporan.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="flbl">Tipe Laporan *</label>
                            <select name="tipe" class="finp" required>
                                <option value="">Pilih tipe...</option>
                                <option value="kualitas_air">Kualitas Air</option>
                                <option value="pakan">Pakan</option>
                                <option value="pengurasan">Pengurasan Air</option>
                                <option value="panen">Panen</option>
                                <option value="bulanan">Bulanan (Semua)</option>
                                <option value="mingguan">Mingguan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Periode Mulai *</label>
                            <input type="date" name="periode_mulai" class="finp" required>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Periode Selesai *</label>
                            <input type="date" name="periode_selesai" class="finp" required>
                        </div>
                        <div class="col-12">
                            <label class="flbl">Kolam (opsional)</label>
                            <select name="kolam_id" class="finp">
                                <option value="">Semua Kolam</option>
                                @foreach(\App\Models\Kolam::where('status','aktif')->get() as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kolam }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Generate Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection