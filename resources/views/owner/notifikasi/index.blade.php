@extends('layouts.owner')
@section('title','Notifikasi')
@section('page-title','Notifikasi')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div>
        <h1>Notifikasi</h1>
        <p>Pemberitahuan dari sistem dan balasan Admin</p>
    </div>

    <div style="display:flex;gap:8px;align-items:center">
        @php
            $unread = \App\Models\Notifikasi::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
        @endphp

        @if($unread > 0)
        <span style="background:var(--danger);color:#fff;font-size:.75rem;font-weight:700;padding:4px 12px;border-radius:20px">
            {{ $unread }} belum dibaca
        </span>
        @endif

        <form action="{{ route('owner.notifikasi.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn-s">
                <i data-lucide="check-check" style="width:15px;height:15px"></i>
                Tandai Semua
            </button>
        </form>
    </div>
</div>

<div class="card-a">
    @if($notifikasi->count())

    @php
        $icons = [
            'info'    => 'info',
            'warning' => 'alert-triangle',
            'danger'  => 'alert-circle',
            'success' => 'check-circle',
            'flag'    => 'message-square',
        ];

        $colors = [
            'info'    => 'var(--primary)',
            'warning' => 'var(--warning)',
            'danger'  => 'var(--danger)',
            'success' => 'var(--success)',
            'flag'    => 'var(--primary)',
        ];

        $bgs = [
            'info'    => 'rgba(14,124,123,.1)',
            'warning' => 'rgba(244,162,97,.1)',
            'danger'  => 'rgba(230,57,70,.1)',
            'success' => 'rgba(45,198,83,.1)',
            'flag'    => 'rgba(14,124,123,.1)',
        ];
    @endphp

    <div style="display:flex;flex-direction:column">

        @foreach($notifikasi as $n)

        @php
            $targetUrl = match($n->referensi_type) {
                'komentar' => route('owner.komentar.index') . '#flag-' . $n->referensi_id,
                'jadwal_pakan'  => route('owner.jadwal-pakan.index'),
                'jadwal_panen'  => route('owner.panen.index'),
                'pengurasan_air'=> route('owner.pengurasan.index'),
                'kolam'         => route('owner.kualitas-air.show', $n->referensi_id ?? 0),
                default         => null,
            };
        @endphp

        <div id="notif-{{ $n->id }}"
             onclick='handleNotif({{ $n->id }}, @json($targetUrl), this)'
             style="
                display:flex;
                align-items:flex-start;
                gap:14px;
                padding:14px 0;
                border-bottom:1px solid var(--border);
                cursor:pointer;
                transition:background .12s;
                {{ !$n->is_read ? 'background:var(--primary-lt);margin:0 -22px;padding:14px 22px;' : '' }}
             ">

            <div style="
                width:40px;
                height:40px;
                border-radius:10px;
                background:{{ $bgs[$n->tipe] ?? $bgs['info'] }};
                display:flex;
                align-items:center;
                justify-content:center;
                flex-shrink:0
            ">
                <i data-lucide="{{ $icons[$n->tipe] ?? 'info' }}"
                   style="
                    width:18px;
                    height:18px;
                    color:{{ $colors[$n->tipe] ?? $colors['info'] }}
                   ">
                </i>
            </div>

            <div style="flex:1;min-width:0">

                <div style="
                    display:flex;
                    align-items:center;
                    gap:8px;
                    flex-wrap:wrap;
                    margin-bottom:3px
                ">
                    <span style="font-weight:{{ $n->is_read ? '500' : '700' }};font-size:.9rem">
                        {{ $n->judul }}
                    </span>

                    @if($n->tipe === 'flag')
                    <span style="
                        font-size:.67rem;
                        font-weight:600;
                        padding:2px 7px;
                        border-radius:20px;
                        background:#dbeafe;
                        color:#1d4ed8
                    ">
                        BALASAN
                    </span>
                    @endif
                </div>

                <div style="
                    font-size:.84rem;
                    color:var(--muted);
                    line-height:1.45
                ">
                    {{ $n->pesan }}
                </div>

                <div style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                    margin-top:5px;
                    flex-wrap:wrap
                ">
                    <span style="font-size:.71rem;color:var(--muted)">
                        {{ $n->created_at->diffForHumans() }}
                    </span>

                    @if($n->referensi_type)
                    <span style="
                        font-size:.7rem;
                        color:var(--primary);
                        font-weight:500;
                        text-transform:capitalize
                    ">
                        {{ str_replace('_',' ', $n->referensi_type) }}

                        @if($n->referensi_id)
                            #{{ $n->referensi_id }}
                        @endif
                    </span>
                    @endif

                    @if($targetUrl)
                    <span style="
                        font-size:.7rem;
                        color:var(--primary);
                        font-weight:600
                    ">
                        Lihat →
                    </span>
                    @endif
                </div>
            </div>

            @if(!$n->is_read)
            <div style="
                width:9px;
                height:9px;
                border-radius:50%;
                background:var(--primary);
                flex-shrink:0;
                margin-top:6px
            "></div>
            @endif
        </div>

        @endforeach
    </div>

    <div class="mt-3">
        {{ $notifikasi->links() }}
    </div>

    @else

    <div class="empty">
        <i data-lucide="bell"
           style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>

        <h3>Tidak Ada Notifikasi</h3>

        <p>
            Notifikasi akan muncul saat ada aktivitas sistem atau balasan Admin
        </p>
    </div>

    @endif
</div>
@endsection

@push('scripts')
<script>
function handleNotif(id, url, el) {

    fetch(`/owner/notifikasi/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {

        el.style.background = '';
        el.style.margin = '';
        el.style.padding = '14px 0';

        el.querySelector('[style*="border-radius:50%"]')?.remove();

        updateSidebarBadge('/owner/notifikasi/count');

        if(url && url !== 'null') {
            setTimeout(() => {
                window.location.href = url;
            }, 200);
        }
    });
}

function updateSidebarBadge(countUrl) {

    fetch(countUrl)
    .then(r => r.json())
    .then(data => {

        document.querySelectorAll('.nav-badge').forEach(badge => {

            if(badge.closest('a')?.href?.includes('notifikasi')) {

                if(data.count > 0) {
                    badge.textContent = data.count;
                } else {
                    badge.remove();
                }
            }
        });
    });
}
</script>
@endpush