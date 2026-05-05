@extends('layouts.admin')
@section('title','Panen')
@section('page-title','Manajemen Panen')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Jadwal & Hasil Panen</h1><p>Kelola jadwal dan catat hasil panen kolam lele</p></div>
    <button class="btn-p" data-bs-toggle="modal" data-bs-target="#mTambah">
        <i data-lucide="plus" style="width:15px;height:15px"></i> Tambah Jadwal
    </button>
</div>

<div class="card-a">
    @if($jadwalPanen->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Kolam</th><th>Tgl Rencana</th><th>Est. Berat</th><th>Est. Jumlah</th><th>Status</th><th>Flag</th><th>Hasil Panen</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($jadwalPanen as $jp)
                @php
                    $stMap=['pending'=>'bdg-menunggu','approved'=>'bdg-aman','rejected'=>'bdg-kritis','selesai'=>'bdg-aktif','ditunda'=>'bdg-asam'];
                @endphp
                <tr>
                    <td style="font-weight:600">{{ $jp->kolam?->nama_kolam }}</td>
                    <td>{{ $jp->tanggal_rencana->format('d M Y') }}</td>
                    <td>{{ $jp->estimasi_berat ? number_format($jp->estimasi_berat).' kg' : '–' }}</td>
                    <td>{{ $jp->estimasi_jumlah ? number_format($jp->estimasi_jumlah).' ekor' : '–' }}</td>
                    <td><span class="bdg {{ $stMap[$jp->status]??'' }}">{{ ucfirst($jp->status) }}</span></td>
                    <td>
                        @if($jp->flag_status !== 'none')
                            <span class="bdg bdg-flagged">{{ ucfirst($jp->flag_status) }}</span>
                        @else <span style="color:var(--muted);font-size:.8rem">–</span> @endif
                    </td>
                    <td>
                        @if($jp->hasilPanen)
                            <div style="font-weight:600;font-size:.84rem">{{ number_format($jp->hasilPanen->total_berat) }} kg</div>
                            <div style="font-size:.71rem;color:var(--muted)">{{ $jp->hasilPanen->tanggal_panen->format('d/m/Y') }}</div>
                        @else <span style="color:var(--muted);font-size:.8rem">Belum</span> @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap">
                            @if(!$jp->hasilPanen && $jp->status==='approved')
                            <button class="btn-p" style="padding:5px 10px;font-size:.74rem" onclick="openHasil({{ $jp->id }},{{ $jp->kolam_id }})">
                                Input Hasil
                            </button>
                            @endif
                            @if($jp->status==='pending')
                            <button class="btn-s" style="padding:5px 10px;font-size:.74rem" onclick='openEdit(@json($jp))'>
                                <i data-lucide="pencil" style="width:12px;height:12px"></i>
                            </button>
                            @endif
                            <form action="{{ route('admin.panen.jadwal.destroy',$jp) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button class="btn-d" style="padding:5px 10px;font-size:.74rem">
                                    <i data-lucide="trash-2" style="width:12px;height:12px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $jadwalPanen->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="package" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Jadwal Panen</h3>
        <button class="btn-p mt-3" data-bs-toggle="modal" data-bs-target="#mTambah">Tambah Jadwal Panen</button>
    </div>
    @endif
</div>

{{-- Modal Tambah Jadwal --}}
<div class="modal fade modal-a" id="mTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Jadwal Panen</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('admin.panen.jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="flbl">Kolam *</label>
                            <select name="kolam_id" class="finp" required>
                                <option value="">Pilih kolam...</option>
                                @foreach(\App\Models\Kolam::where('status','aktif')->get() as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kolam }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="flbl">Tanggal Rencana Panen *</label>
                            <input type="date" name="tanggal_rencana" class="finp" required min="{{ date('Y-m-d',strtotime('+1 day')) }}">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Estimasi Berat (kg)</label>
                            <input type="number" name="estimasi_berat" class="finp" step="0.1" min="0" placeholder="100">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Estimasi Jumlah (ekor)</label>
                            <input type="number" name="estimasi_jumlah" class="finp" min="0" placeholder="500">
                        </div>
                        <div class="col-12">
                            <label class="flbl">Catatan</label>
                            <textarea name="catatan" class="finp" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Input Hasil Panen --}}
<div class="modal fade modal-a" id="mHasil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Input Hasil Panen</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('admin.panen.hasil.store') }}" method="POST">
                @csrf
                <input type="hidden" id="h_jadwal" name="jadwal_panen_id">
                <input type="hidden" id="h_kolam"  name="kolam_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="flbl">Tanggal Panen *</label>
                            <input type="date" name="tanggal_panen" class="finp" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Total Berat (kg) *</label>
                            <input type="number" name="total_berat" class="finp" step="0.01" min="0" required placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Total Jumlah (ekor)</label>
                            <input type="number" name="total_jumlah" class="finp" min="0" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Harga / kg (Rp)</label>
                            <input type="number" name="harga_per_kg" class="finp" step="100" min="0" placeholder="25000">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Kualitas</label>
                            <select name="kualitas" class="finp">
                                <option value="baik">Baik</option>
                                <option value="sedang">Sedang</option>
                                <option value="buruk">Buruk</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="flbl">Catatan</label>
                            <textarea name="catatan" class="finp" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Simpan Hasil Panen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openHasil(jadwalId, kolamId) {
    document.getElementById('h_jadwal').value = jadwalId;
    document.getElementById('h_kolam').value  = kolamId;
    new bootstrap.Modal(document.getElementById('mHasil')).show();
}
function openEdit(jp) {
    // bisa tambahkan modal edit jadwal di sini
}
</script>
@endpush