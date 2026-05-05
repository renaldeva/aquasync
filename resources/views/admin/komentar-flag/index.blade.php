@extends('layouts.admin')
@section('title','Komentar & Flag')
@section('page-title','Komentar & Flag')

@section('content')
<div class="ph"><h1>Komentar & Flag</h1><p>Kelola masukan dan penolakan dari Owner</p></div>

<div class="card-a">
    @if($flags->count())
    <div style="display:flex;flex-direction:column;gap:14px">
        @foreach($flags as $flag)
        <div id="flag-{{ $flag->id }}"
             style="border:1px solid var(--border);border-radius:var(--r-sm);padding:18px;
                    {{ $flag->status==='menunggu' ? 'border-left:3px solid var(--warning)' : '' }}">

            {{-- Header --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem;flex-shrink:0">
                        {{ strtoupper(substr($flag->user->name,0,1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.9rem">{{ $flag->user->name }}</div>
                        <div style="font-size:.71rem;color:var(--muted)">
                            {{ $flag->created_at->format('d/m/Y H:i') }} · {{ $flag->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span class="bdg bdg-menunggu" style="font-size:.69rem;text-transform:capitalize">
                        {{ str_replace('_',' ',$flag->target_type) }}
                    </span>
                    @php $sc=['menunggu'=>'bdg-asam','dibalas'=>'bdg-aktif','selesai'=>'bdg-aman','ditolak'=>'bdg-kritis'][$flag->status]??'bdg-menunggu' @endphp
                    <span class="bdg {{ $sc }}" style="font-size:.69rem">{{ ucfirst($flag->status) }}</span>

                    {{-- Update Status --}}
                    <form action="{{ route('admin.komentar-flag.status',$flag) }}" method="POST" style="display:inline">
                        @csrf @method('PATCH')
                        <select name="status" class="finp" style="padding:4px 8px;font-size:.74rem;width:auto;height:auto" onchange="this.form.submit()">
                            @foreach(['menunggu','dibalas','selesai','ditolak'] as $s)
                            <option value="{{ $s }}" {{ $flag->status===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            {{-- Pesan Owner --}}
            <div style="background:var(--body-bg);border-radius:8px;padding:13px;font-size:.875rem;margin-bottom:12px;line-height:1.55">
                "{{ $flag->isi_komentar }}"
            </div>

            {{-- Balasan Admin --}}
            @if($flag->isi_balasan)
            <div style="background:var(--primary-lt);border-radius:8px;padding:13px;font-size:.84rem;margin-bottom:12px">
                <div style="font-size:.71rem;font-weight:600;color:var(--primary);margin-bottom:5px">
                    <i data-lucide="corner-down-right" style="width:12px;height:12px;margin-right:3px"></i>
                    Balasan Admin · {{ $flag->dibalas_at?->format('d/m/Y H:i') }}
                </div>
                {{ $flag->isi_balasan }}
            </div>
            @endif

            {{-- Form Balas --}}
            @if($flag->status === 'menunggu')
            <form action="{{ route('admin.komentar-flag.balas',$flag) }}" method="POST">
                @csrf
                <div style="display:flex;gap:8px">
                    <input type="text" name="isi_balasan" class="finp" placeholder="Tulis balasan untuk owner..." required style="flex:1">
                    <button type="submit" class="btn-p" style="flex-shrink:0;padding:10px 18px">Kirim Balasan</button>
                </div>
            </form>
            @endif
        </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $flags->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="flag" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Tidak Ada Flag atau Komentar</h3>
        <p>Semua aktivitas berjalan dengan baik</p>
    </div>
    @endif
</div>
@endsection