@extends('layouts.manager')
@section('title', 'Dashboard')
@section('content')
<div class="top">
  <div><h1>Dashboard</h1><p>Halo, {{ auth()->user()->name }} — ini kondisi mesin hari ini.</p></div>
  <div class="actions">
    <a class="btn" href="{{ route('manajemen.lapor') }}">@include('partials.icon', ['n' => 'plus']) Lapor Kerusakan</a>
    <a class="btn primary" href="{{ route('manajemen.laporan.index') }}">@include('partials.icon', ['n' => 'file']) Buat Laporan</a>
  </div>
</div>

<div class="grid g4" style="margin-bottom:22px">
  <div class="stat"><div class="ic">@include('partials.icon', ['n' => 'cog'])</div><span>Mesin aktif</span><b>{{ $stats['aktif'] }}</b></div>
  <div class="stat s2"><div class="ic">@include('partials.icon', ['n' => 'wrench'])</div><span>Sedang diperbaiki</span><b>{{ $stats['diperbaiki'] }}</b></div>
  <div class="stat s3"><div class="ic">@include('partials.icon', ['n' => 'clock'])</div><span>Menunggu teknisi</span><b>{{ $stats['menunggu'] }}</b></div>
  <div class="stat s4"><div class="ic">@include('partials.icon', ['n' => 'alert'])</div><span>Melewati batas waktu</span><b>{{ $stats['terlambat'] }}</b></div>
</div>

<div class="card tablewrap">
  <div class="row between"><h2>Tiket Terbaru</h2><a class="link" href="{{ route('manajemen.perbaikan.index') }}">Lihat semua →</a></div>
  <table>
    <thead><tr><th>Tiket</th><th>Kode mesin</th><th>Lokasi</th><th>Urgensi</th><th>Aksi</th><th>Status</th></tr></thead>
    <tbody>
    @forelse ($tickets as $t)
      <tr>
        <td><b>{{ $t->code }}</b></td>
        <td>{{ $t->machine->code }}</td>
        <td>{{ $t->machine->location }}</td>
        <td>@include('partials.chip', ['v' => $t->urgency])</td>
        <td><a class="link" href="{{ route('manajemen.perbaikan.show', $t) }}">detail</a></td>
        <td>@include('partials.chip', ['v' => $t->display_status])</td>
      </tr>
    @empty
      <tr><td colspan="6" class="empty">Belum ada tiket.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="grid g2" style="margin-top:18px">
  <div class="card">
    <h3>Tiket 6 bulan terakhir</h3>
    @php $max = max(1, $trend->max('count')); @endphp
    <div class="bars">
      @foreach ($trend as $b)
        <div><i style="height:{{ max(6, $b['count'] / $max * 100) }}%"><b>{{ $b['count'] }}</b></i>{{ $b['label'] }}</div>
      @endforeach
    </div>
  </div>
  <div class="card">
    <div class="row between"><h3>Jadwal terdekat</h3><a class="link" href="{{ route('manajemen.jadwal.index') }}">Semua</a></div>
    @forelse ($upcoming as $s)
      <div class="row between" style="padding:10px 0;border-top:1px solid var(--line)">
        <div><b>{{ $s->machine->label }}</b><div class="small muted">{{ $s->task }} · {{ $s->due_date->translatedFormat('d M Y') }}</div></div>
        @include('partials.chip', ['v' => $s->display_status])
      </div>
    @empty
      <div class="empty">Tidak ada jadwal.</div>
    @endforelse
  </div>
</div>
@endsection
