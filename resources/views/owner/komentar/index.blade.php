@extends('layouts.owner')
@section('title','Komentar & Flag')
@section('page-title','Komentar & Flag')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Komentar & Flag Saya</h1><p>Kelola masukan dan penolakan yang Anda kirimkan ke Admin</p></div>
    <button class="btn-p" data-bs-toggle="modal" data-bs-target="#mKirim">
        <i data-lucide="plus" style="width:15px;height:15px"></i> Kirim Komentar / Flag
    </button>
</div>

<div class="card-a">
    @if($flags->count())
    <div style="display:flex;flex-direction:column;gap:14px">
        @foreach($flags as $flag)
        <div style="border:1px solid var(--border);border-radius:var(--r-sm);padding:18px;
                    {{ $flag->status==='menunggu' ? 'border-left:3px solid var(--warning)' : ($flag->status==='dibalas' ? 'border-left:3px solid var(--primary)' : '') }}">

            {{-- Header --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px">
                <div style="display:flex;align-items:center;gap:8px">
                    <span style="font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:20px;background:var(--body-bg);color:var(--muted);text-transform:capitalize">
                        {{ str_replace('_',' ',$flag->target_type) }}
                    </span>
                    <span style="font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:20px;
                        {{ $flag->jenis==='flag' ? 'background:#f8d7da;color:#721c24' : 'background:rgba(14,124,123,.1);color:var(--primary)' }}">
                        {{ ucfirst($flag->jenis) }}
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    @php $sc=['menunggu'=>'bdg-asam','dibalas'=>'bdg-dibalas','selesai'=>'bdg-selesai','ditolak'=>'bdg-kritis'][$flag->status]??'bdg-menunggu' @endphp
                    <span class="bdg {{ $sc }}" style="font-size:.69rem">{{ ucfirst($flag->status) }}</span>
                    <span style="font-size:.71rem;color:var(--muted)">{{ $flag->created_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Pesan Saya --}}
            <div style="background:var(--body-bg);border-radius:8px;padding:13px;font-size:.875rem;line-height:1.55;margin-bottom:12px">
                "{{ $flag->isi_komentar }}"
            </div>

            {{-- Balasan Admin --}}
            @if($flag->isi_balasan)
            <div style="background:var(--primary-lt);border-radius:8px;padding:13px;font-size:.84rem">
                <div style="font-size:.71rem;font-weight:600;color:var(--primary);margin-bottom:5px;display:flex;align-items:center;gap:4px">
                    <i data-lucide="corner-down-right" style="width:12px;height:12px"></i>
                    Balasan Admin · {{ $flag->dibalas_at?->format('d/m/Y H:i') }}
                </div>
                {{ $flag->isi_balasan }}
            </div>
            @else
            <div style="font-size:.79rem;color:var(--muted);display:flex;align-items:center;gap:6px">
                <i data-lucide="clock" style="width:13px;height:13px"></i>
                Menunggu balasan dari Admin
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $flags->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="message-square" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Belum Ada Komentar atau Flag</h3>
        <p>Kirim komentar atau flag ke Admin untuk memberikan masukan</p>
        <button class="btn-p mt-3" data-bs-toggle="modal" data-bs-target="#mKirim">
            <i data-lucide="plus" style="width:15px;height:15px"></i> Kirim Sekarang
        </button>
    </div>
    @endif
</div>

{{-- Modal Kirim Komentar / Flag --}}
<div class="modal fade modal-a" id="mKirim" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Komentar / Flag ke Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('owner.komentar.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="flbl">Jenis *</label>
                            <select name="jenis" class="finp" required>
                                <option value="komentar">Komentar</option>
                                <option value="flag">Flag (Penolakan)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="flbl">Terkait *</label>
                            <select name="target_type" class="finp" required id="targetType" onchange="updateTargetId()">
                                <option value="jadwal_pakan">Jadwal Pakan</option>
                                <option value="jadwal_panen">Jadwal Panen</option>
                                <option value="pengurasan_air">Pengurasan Air</option>
                                <option value="kolam">Kolam</option>
                                <option value="laporan">Laporan</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="flbl">ID Target</label>
                            <input type="number" name="target_id" class="finp" placeholder="Isi ID item yang dimaksud" required min="1">
                            <span style="font-size:.73rem;color:var(--muted);margin-top:3px;display:block">
                                Lihat ID dari halaman monitoring yang sesuai
                            </span>
                        </div>
                        <div class="col-12">
                            <label class="flbl">Pesan *</label>
                            <textarea name="isi_komentar" class="finp" rows="4"
                                placeholder="Tulis komentar atau alasan penolakan Anda..." required maxlength="1000"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-p">Kirim ke Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection