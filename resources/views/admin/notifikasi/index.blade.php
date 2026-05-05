@extends('layouts.admin')
@section('title','Notifikasi')
@section('page-title','Notifikasi')

@section('content')
<div class="ph d-flex align-items-start justify-content-between">
    <div><h1>Notifikasi</h1><p>Pemberitahuan sistem, IoT, dan aktivitas owner</p></div>
    <form action="{{ route('admin.notifikasi.read-all') }}" method="POST">
        @csrf
        <button type="submit" class="btn-s">
            <i data-lucide="check-check" style="width:15px;height:15px"></i> Tandai Semua Dibaca
        </button>
    </form>
</div>

<div class="card-a">
    @if($notifikasi->count())
    @php
        $icons  = ['info'=>'info','warning'=>'alert-triangle','danger'=>'alert-circle','success'=>'check-circle','flag'=>'flag'];
        $colors = ['info'=>'var(--primary)','warning'=>'var(--warning)','danger'=>'var(--danger)','success'=>'var(--success)','flag'=>'var(--danger)'];
        $bgs    = ['info'=>'rgba(29,111,164,.1)','warning'=>'rgba(244,162,97,.1)','danger'=>'rgba(230,57,70,.1)','success'=>'rgba(45,198,83,.1)','flag'=>'rgba(230,57,70,.1)'];
    @endphp
    <div style="display:flex;flex-direction:column">
        @foreach($notifikasi as $n)
        <div onclick="markRead({{ $n->id }},this)"
             style="display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);cursor:pointer;transition:background .12s;
                    {{ !$n->is_read ? 'background:var(--primary-lt);margin:0 -22px;padding:14px 22px' : '' }}">
            <div style="width:38px;height:38px;border-radius:10px;background:{{ $bgs[$n->tipe]??$bgs['info'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i data-lucide="{{ $icons[$n->tipe]??'info' }}" style="width:17px;height:17px;color:{{ $colors[$n->tipe]??$colors['info'] }}"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:{{ $n->is_read?'500':'700' }};font-size:.9rem;margin-bottom:3px">{{ $n->judul }}</div>
                <div style="font-size:.84rem;color:var(--muted);line-height:1.45">{{ $n->pesan }}</div>
                <div style="font-size:.71rem;color:var(--muted);margin-top:4px">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            @if(!$n->is_read)
            <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:6px"></div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $notifikasi->links() }}</div>
    @else
    <div class="empty">
        <i data-lucide="bell" style="width:48px;height:48px;display:block;margin:0 auto 12px;opacity:.25"></i>
        <h3>Tidak Ada Notifikasi</h3>
        <p>Semua notifikasi akan muncul di sini</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markRead(id, el) {
    fetch(`/admin/notifikasi/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => {
        el.style.background = '';
        el.style.margin     = '';
        el.style.padding    = '14px 0';
        const dot = el.querySelector('[style*="border-radius:50%;background:var(--primary)"]');
        if (dot) dot.remove();
    });
}
</script>
@endpush