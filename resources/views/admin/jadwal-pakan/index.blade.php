@extends('layouts.admin')
@section('title','Jadwal Pakan')
@section('page-title','Manajemen Jadwal Pakan')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Jadwal Pakan</h1><p>Atur jadwal pemberian pakan otomatis via IoT</p></div>
    <button class="btn-p" data-bs-toggle="modal" data-bs-target="#mTambah">
        <i data-lucide="plus" style="width:15px;height:15px"></i> Tambah Jadwal
    </button>
</div>

<div class="card-a">
    @if($jadwal->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr><th>Nama Jadwal</th><th>Kolam</th><th>Waktu</th><th>Jumlah</th><th>Jenis Pakan</th><th>Frekuensi</th><th>Status</th><th>Flag</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($jadwal as $j)
                <tr>
                    <td style="font-weight:600">{{ $j->nama_jadwal ?? 'Jadwal Pakan' }}</td>
                    <td>
                        <div style="font-weight:500">{{ $j->kolam?->nama_kolam }}</div>
                        <div style="font-size:.71rem;color:var(--muted)">{{ $j->kolam?->kode_kolam }}</div>
                    </td>
                    <td>
                        <span class="mono" style="font-weight:700;font-size:.94rem">{{ substr($j->waktu_pakan,0,5) }}</span>
                        <span style="font-size:.71rem;color:var(--muted)"> WIB</span>
                    </td>
                    <td>{{ $j->jumlah_pakan ? number_format($j->jumlah_pakan).' '.$j->satuan : '–' }}</td>
                    <td>{{ $j->jenis_pakan ?? '–' }}</td>
                    <td style="text-transform:capitalize">{{ $j->frekuensi }}</td>
                    <td><span class="bdg {{ $j->status==='aktif' ? 'bdg-aktif' : 'bdg-nonaktif' }}">{{ ucfirst($j->status) }}</span></td>
                    <td>
                        @if($j->flag_status !== 'none')
                            <span class="bdg bdg-flagged">{{ ucfirst($j->flag_status) }}</span>
                        @else <span style="color:var(--muted);font-size:.8rem">–</span> @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px">
                            <form action="{{ route('admin.jadwal-pakan.execute',$j) }}" method="POST">
                                @csrf
                                <button class="btn-s" style="padding:5px 10px;font-size:.74rem" title="Eksekusi sekarang">
                                    <i data-lucide="play" style="width:12px;height:12px"></i>
                                </button>
                            </form>
                            <button class="btn-s" style="padding:5px 10px;font-size:.74rem" onclick='openEdit(@json($j))'>
                                <i data-lucide="pencil" style="width:12px;height:12px"></i>
                            </button>
                            <form action="{{ route('admin.jadwal-pakan.destroy',$j) }}" method="POST" onsubmit="return confirm('Nonaktifkan jadwal ini?')">
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
    <div class="mt-3">{{ $jadwal->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="calendar-clock" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Jadwal Pakan</h3>
        <button class="btn-p mt-3" data-bs-toggle="modal" data-bs-target="#mTambah">Tambah Jadwal</button>
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade modal-a" id="mTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Jadwal Pakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('admin.jadwal-pakan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="flbl">Kolam *</label>
                            <select name="kolam_id" class="finp" required>
                                <option value="">Pilih kolam...</option>
                                @foreach($kolam as $k)<option value="{{ $k->id }}">{{ $k->nama_kolam }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Nama Jadwal</label>
                            <input type="text" name="nama_jadwal" class="finp" placeholder="Pakan Pagi">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Waktu Pakan *</label>
                            <input type="time" name="waktu_pakan" class="finp" required>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Jumlah Pakan</label>
                            <input type="number" name="jumlah_pakan" class="finp" placeholder="500" step="0.01" min="0">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Satuan</label>
                            <select name="satuan" class="finp">
                                <option value="gram">Gram</option>
                                <option value="kg">Kilogram</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="flbl">Jenis Pakan</label>
                            <input type="text" name="jenis_pakan" class="finp" placeholder="Pakan Lele Premium">
                        </div>
                        <div class="col-12">
                            <label class="flbl">Frekuensi</label>
                            <select name="frekuensi" class="finp">
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="custom">Custom</option>
                            </select>
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

{{-- Modal Edit --}}
<div class="modal fade modal-a" id="mEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Jadwal Pakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="fEdit" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="flbl">Nama Jadwal</label>
                            <input type="text" id="e_nama" name="nama_jadwal" class="finp">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Waktu Pakan *</label>
                            <input type="time" id="e_waktu" name="waktu_pakan" class="finp" required>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Jumlah</label>
                            <input type="number" id="e_jumlah" name="jumlah_pakan" class="finp" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Status</label>
                            <select id="e_status" name="status" class="finp">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(j) {
    document.getElementById('fEdit').action = `/admin/jadwal-pakan/${j.id}`;
    document.getElementById('e_nama').value   = j.nama_jadwal ?? '';
    document.getElementById('e_waktu').value  = (j.waktu_pakan ?? '').substring(0,5);
    document.getElementById('e_jumlah').value = j.jumlah_pakan ?? '';
    document.getElementById('e_status').value = j.status;
    new bootstrap.Modal(document.getElementById('mEdit')).show();
}
</script>
@endpush