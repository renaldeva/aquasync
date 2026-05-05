@extends('layouts.admin')
@section('title','Data Kolam')
@section('page-title','Data Kolam')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Data Kolam</h1><p>Kelola seluruh data kolam lele budidaya</p></div>
    <button class="btn-p" data-bs-toggle="modal" data-bs-target="#mTambah">
        <i data-lucide="plus" style="width:15px;height:15px"></i> Tambah Kolam
    </button>
</div>

<div class="card-a">
    @if($kolam->count())
    <div style="overflow-x:auto">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode</th><th>Nama Kolam</th><th>Jenis</th>
                    <th>pH Terkini</th><th>Kapasitas</th><th>Jumlah Ikan</th>
                    <th>Status</th><th>Jadwal</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kolam as $k)
                @php $latest = $k->latestKualitasAir; @endphp
                <tr>
                    <td><span class="mono" style="font-size:.79rem;background:var(--body-bg);padding:3px 8px;border-radius:4px">{{ $k->kode_kolam }}</span></td>
                    <td><div style="font-weight:600">{{ $k->nama_kolam }}</div></td>
                    <td style="text-transform:capitalize">{{ $k->jenis }}</td>
                    <td>
                        @if($latest)
                            @php $c=match($latest->status_ph){'normal'=>'bdg-normal','asam'=>'bdg-asam','basa'=>'bdg-basa','kritis'=>'bdg-kritis',default=>'bdg-menunggu'} @endphp
                            <span class="bdg {{ $c }}">{{ number_format($latest->ph_value,1) }}</span>
                        @else <span style="color:var(--muted);font-size:.8rem">–</span> @endif
                    </td>
                    <td>{{ $k->kapasitas_liter ? number_format($k->kapasitas_liter).' L' : '–' }}</td>
                    <td>{{ number_format($k->jumlah_ikan) }} ekor</td>
                    <td><span class="bdg {{ $k->status==='aktif' ? 'bdg-aktif' : 'bdg-nonaktif' }}">{{ ucfirst($k->status) }}</span></td>
                    <td style="font-size:.82rem">{{ $k->jadwal_aktif ?? 0 }} aktif</td>
                    <td>
                        <div style="display:flex;gap:5px">
                            <a href="{{ route('admin.kualitas-air.show', $k->id) }}" class="btn-s" style="padding:5px 10px;font-size:.74rem">
                                <i data-lucide="activity" style="width:12px;height:12px"></i>
                            </a>
                            <button class="btn-s" style="padding:5px 10px;font-size:.74rem" onclick='openEdit(@json($k))'>
                                <i data-lucide="pencil" style="width:12px;height:12px"></i>
                            </button>
                            <form action="{{ route('admin.kolam.destroy',$k) }}" method="POST" onsubmit="return confirm('Nonaktifkan kolam ini?')">
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
    <div class="mt-3">{{ $kolam->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="grid-2x2" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Data Kolam</h3>
        <p>Mulai dengan menambahkan kolam pertama</p>
        <button class="btn-p mt-3" data-bs-toggle="modal" data-bs-target="#mTambah">
            <i data-lucide="plus" style="width:15px;height:15px"></i> Tambah Kolam
        </button>
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade modal-a" id="mTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kolam Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kolam.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="flbl">Nama Kolam *</label>
                            <input type="text" name="nama_kolam" class="finp" placeholder="cth: Kolam Pembesaran A" required>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Jenis *</label>
                            <select name="jenis" class="finp" required>
                                <option value="">Pilih jenis...</option>
                                <option value="pembesaran">Pembesaran</option>
                                <option value="pendederan">Pendederan</option>
                                <option value="induk">Induk</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Kapasitas (Liter)</label>
                            <input type="number" name="kapasitas_liter" class="finp" placeholder="5000" step="0.01" min="0">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Jumlah Ikan (ekor)</label>
                            <input type="number" name="jumlah_ikan" class="finp" placeholder="500" min="0">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Tanggal Tebar</label>
                            <input type="date" name="tanggal_tebar" class="finp">
                        </div>
                        <div class="col-12">
                            <label class="flbl">Keterangan</label>
                            <textarea name="keterangan" class="finp" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Simpan Kolam</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade modal-a" id="mEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kolam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fEdit" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="flbl">Nama Kolam *</label>
                            <input type="text" id="e_nama" name="nama_kolam" class="finp" required>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Jenis *</label>
                            <select id="e_jenis" name="jenis" class="finp" required>
                                <option value="pembesaran">Pembesaran</option>
                                <option value="pendederan">Pendederan</option>
                                <option value="induk">Induk</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Status</label>
                            <select id="e_status" name="status" class="finp">
                                <option value="aktif">Aktif</option>
                                <option value="kosong">Kosong</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Kapasitas (Liter)</label>
                            <input type="number" id="e_kapasitas" name="kapasitas_liter" class="finp" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="flbl">Jumlah Ikan</label>
                            <input type="number" id="e_jumlah" name="jumlah_ikan" class="finp">
                        </div>
                        <div class="col-12">
                            <label class="flbl">Keterangan</label>
                            <textarea id="e_ket" name="keterangan" class="finp" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(k) {
    document.getElementById('fEdit').action = `/admin/kolam/${k.id}`;
    document.getElementById('e_nama').value     = k.nama_kolam;
    document.getElementById('e_jenis').value    = k.jenis;
    document.getElementById('e_status').value   = k.status;
    document.getElementById('e_kapasitas').value = k.kapasitas_liter ?? '';
    document.getElementById('e_jumlah').value   = k.jumlah_ikan ?? '';
    document.getElementById('e_ket').value      = k.keterangan ?? '';
    new bootstrap.Modal(document.getElementById('mEdit')).show();
}
</script>
@endpush