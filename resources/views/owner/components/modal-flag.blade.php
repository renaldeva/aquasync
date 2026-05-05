{{-- ============================================================
     Komponen: Modal Flag
     Dipakai di semua halaman monitoring owner
     Cara pakai: @include('owner.components.modal-flag')
     Cara trigger JS: openFlag('target_type', target_id)
     ============================================================ --}}

     <div class="modal fade modal-a" id="mFlag" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Komentar / Flag ke Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('owner.komentar.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="mf_target_type" name="target_type">
                    <input type="hidden" id="mf_target_id"   name="target_id">
    
                    <div class="modal-body">
                        {{-- Preview target --}}
                        <div id="mf_preview" style="background:var(--body-bg);border-radius:var(--r-sm);padding:10px 14px;margin-bottom:16px;font-size:.82rem;display:flex;align-items:center;gap:8px">
                            <i data-lucide="tag" style="width:14px;height:14px;color:var(--primary);flex-shrink:0"></i>
                            <span id="mf_preview_text" style="color:var(--muted)">Memilih target...</span>
                        </div>
    
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="flbl">Jenis *</label>
                                <select name="jenis" class="finp" required>
                                    <option value="komentar">💬 Komentar – Masukan atau saran</option>
                                    <option value="flag">🚩 Flag – Penolakan atau keberatan</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="flbl">Pesan *</label>
                                <textarea name="isi_komentar" class="finp" rows="4"
                                    placeholder="Tulis komentar atau alasan penolakan Anda dengan jelas..."
                                    required maxlength="1000" id="mf_pesan"></textarea>
                                <div style="font-size:.72rem;color:var(--muted);margin-top:4px;text-align:right">
                                    <span id="mf_char">0</span>/1000 karakter
                                </div>
                            </div>
                        </div>
    
                        <div style="margin-top:12px;padding:10px 12px;background:rgba(244,162,97,.08);border:1px solid rgba(244,162,97,.25);border-radius:var(--r-sm);font-size:.79rem;color:#856404;display:flex;align-items:flex-start;gap:7px">
                            <i data-lucide="alert-triangle" style="width:13px;height:13px;flex-shrink:0;margin-top:1px"></i>
                            Flag/komentar akan dikirim ke Admin dan tidak dapat ditarik kembali setelah dikirim.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-s" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-p">
                            <i data-lucide="send" style="width:14px;height:14px"></i> Kirim ke Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
    const typeLabels = {
        'kolam': 'Kolam',
        'jadwal_pakan': 'Jadwal Pakan',
        'jadwal_panen': 'Jadwal Panen',
        'pengurasan_air': 'Pengurasan Air',
        'laporan': 'Laporan',
    };
    
    function openFlag(targetType, targetId) {
        document.getElementById('mf_target_type').value = targetType;
        document.getElementById('mf_target_id').value   = targetId;
        document.getElementById('mf_preview_text').textContent =
            `${typeLabels[targetType] ?? targetType} #${targetId}`;
        document.getElementById('mf_pesan').value = '';
        document.getElementById('mf_char').textContent = '0';
        new bootstrap.Modal(document.getElementById('mFlag')).show();
    }
    
    // Hitung karakter
    document.getElementById('mf_pesan')?.addEventListener('input', function() {
        document.getElementById('mf_char').textContent = this.value.length;
    });
    </script>
    @endpush