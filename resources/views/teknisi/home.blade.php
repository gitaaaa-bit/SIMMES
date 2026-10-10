@extends('layouts.technician')
@section('title', 'Beranda')
@section('heading', 'Halo, '.explode(' ', auth()->user()->name)[0].' 👋')
@section('sub', 'Dashboard Teknisi · '.now()->translatedFormat('l, d F Y'))
@section('content')
<div class="tstats">
  <a class="tstat hot" href="{{ route('teknisi.tugas.index', ['f' => 'belum']) }}"><b>{{ $stats['baru'] }}</b><span>Tugas baru</span></a>
  <a class="tstat" href="{{ route('teknisi.tugas.index', ['f' => 'proses']) }}"><b>{{ $stats['proses'] }}</b><span>Sedang dikerjakan</span></a>
  <a class="tstat" href="{{ route('teknisi.tugas.index', ['f' => 'selesai']) }}"><b>{{ $stats['selesai'] }}</b><span>Selesai bulan ini</span></a>
  <div class="tstat"><b style="color:var(--r600)">{{ $stats['terlambat'] }}</b><span>Melewati batas</span></div>
</div>

<div class="row" style="gap:10px;margin-bottom:14px">
  <a class="btn primary block" href="{{ route('teknisi.laporan.create') }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;font-size:13px;padding:12px 10px">
    @include('partials.icon', ['n' => 'plus']) Buat Laporan
  </a>
  <a class="btn block" href="{{ route('teknisi.laporan.index') }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;font-size:13px;padding:12px 10px">
    @include('partials.icon', ['n' => 'file']) Riwayat Laporan
  </a>
</div>

@if ($reminders->count())
<div class="tcard hero">
  <div class="row between"><b>🔔 Pengingat jadwal</b></div>
  @foreach ($reminders as $r)
    <div class="reminder">
      <span class="rem-tag {{ $r['tone'] === 'late' ? 'late' : '' }}">{{ $r['label'] }}</span>
      <div><b class="small">{{ $r['s']->task }} · {{ $r['s']->machine->code }}</b><div class="tiny">{{ $r['s']->machine->name }} · {{ $r['s']->due_date->translatedFormat('d M') }}</div></div>
    </div>
  @endforeach
</div>
@endif

<div class="sect"><h2>Tugas prioritas</h2><a class="link small" href="{{ route('teknisi.tugas.index') }}">Semua</a></div>
@forelse ($priority as $t)
  <a class="tcard task {{ $t->urgency }}" href="{{ route('teknisi.tugas.show', $t) }}">
    <div class="t1"><span>{{ $t->code }} · {{ $t->machine->code }}</span>@include('partials.chip', ['v' => $t->display_status])</div>
    <p><b>{{ $t->machine->name }}, {{ $t->machine->location }}</b><br><span class="muted small">{{ \Illuminate\Support\Str::limit($t->complaint, 70) }}</span></p>
    <div class="row between">@include('partials.chip', ['v' => $t->urgency])<div class="progress" style="width:90px"><i style="width:{{ $t->progress }}%"></i></div></div>
  </a>
@empty
  <div class="tcard empty">Tidak ada tugas aktif. Santai dulu ☕</div>
@endforelse

<div class="sect"><h2>Jadwal maintenance saya</h2><a class="link small" href="{{ route('teknisi.jadwal') }}">Semua</a></div>
<div class="tcard">
  @forelse ($schedules as $s)
    <div class="reminder"><div style="flex:1"><b class="small">{{ $s->machine->label }}</b><div class="tiny">{{ $s->task }} · {{ $s->due_date->translatedFormat('d M Y') }}</div></div>@include('partials.chip', ['v' => $s->display_status])</div>
  @empty <div class="empty">Belum ada jadwal.</div> @endforelse
</div>

<div class="sect"><h2>Aktivitas 7 hari</h2></div>
<div class="tcard">
  @php $mx = max(1, $week->max('n')); @endphp
  <div class="minibars">@foreach ($week as $w)<div><i style="height:{{ max(7, $w['n'] / $mx * 100) }}%"></i>{{ $w['d'] }}</div>@endforeach</div>
</div>

@if ($recent->count())
<div class="sect"><h2>Baru selesai</h2></div>
<div class="tcard">
  @foreach ($recent as $t)<div class="reminder"><div style="flex:1"><b class="small">{{ $t->code }} · {{ $t->machine->name }}</b><div class="tiny">{{ $t->finished_at?->diffForHumans() }}</div></div>@include('partials.chip', ['v' => 'selesai'])</div>@endforeach
</div>
@endif
@endsection
