@extends('layouts.manager')
@section('title', 'Dashboard')
@section('content')
<div class="top">
  <div>
    <h1>Dashboard</h1>
    <p>Halo, {{ auth()->user()->name }} — monitoring kondisi mesin dan antrian jadwal hari ini.</p>
  </div>
  <div class="actions">
    <a class="btn" href="{{ route('manajemen.jadwal.create') }}">@include('partials.icon', ['n' => 'plus']) Tambah Jadwal</a>
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

{{-- ANTRIAN JADWAL MAINTENANCE (BARU DIMASUKKAN DI ATAS) --}}
<div class="card tablewrap" style="margin-bottom:22px">
  <div class="row between" style="margin-bottom:12px">
    <div>
      <div style="display:flex;align-items:center;gap:8px">
        <h2 style="margin:0">Antrian Jadwal Maintenance</h2>
        <span class="chip" style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:700">Terbaru di Atas</span>
      </div>
      <div class="small muted" style="margin-top:4px">Jadwal yang baru ditambahkan langsung masuk di posisi antrian paling atas.</div>
    </div>
    <div class="row" style="gap:8px">
      <a class="btn" href="{{ route('manajemen.jadwal.create') }}" style="font-size:12.5px;padding:7px 12px">
        @include('partials.icon', ['n' => 'plus']) Tambah Jadwal
      </a>
      <a class="link" href="{{ route('manajemen.jadwal.index') }}" style="font-size:13px">Lihat semua jadwal →</a>
    </div>
  </div>
  <table>
    <thead>
      <tr>
        <th>Mesin</th>
        <th>Tugas / Jenis</th>
        <th>Waktu Pelaksanaan</th>
        <th>Teknisi</th>
        <th>Status</th>
        <th style="text-align:right">Aksi</th>
      </tr>
    </thead>
    <tbody>
    @forelse ($upcoming as $s)
      @php
        $tech = $s->technician;
      @endphp
      <tr>
        <td>
          <b>{{ $s->machine->label }}</b>
          <div class="tiny muted">{{ $s->machine->location }}</div>
        </td>
        <td>
          <b>{{ $s->task }}</b>
          <div class="tiny muted">{{ ucfirst($s->type) }}{{ $s->interval_label ? ' · '.$s->interval_label : '' }}</div>
        </td>
        <td>
          <b>{{ $s->due_date->translatedFormat('d M Y') }}</b>
          <div class="tiny" style="color:var(--brand);font-weight:700">
            @if ($s->due_time) Jam {{ $s->due_time }} WIB @else - @endif
          </div>
        </td>
        <td>
          @if ($tech)
            <div><b>{{ $tech->name }}</b></div>
            @if ($tech->whatsapp)
              <div class="tiny" style="color:var(--ok);font-weight:600">WA: {{ $tech->whatsapp }}</div>
            @else
              <div class="tiny" style="color:var(--r600)">WA belum diisi</div>
            @endif
          @else
            <span class="muted small">Belum ditugaskan</span>
          @endif
        </td>
        <td>@include('partials.chip', ['v' => $s->display_status])</td>
        <td style="text-align:right">
          <a class="link" href="{{ route('manajemen.mesin.show', $s->machine) }}" style="font-weight:600">
            Detail →
          </a>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="empty">Belum ada antrian jadwal maintenance.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="card tablewrap">
  <div class="row between"><h2>Tiket Terbaru</h2><a class="link" href="{{ route('manajemen.perbaikan.index') }}">Lihat semua tiket →</a></div>
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
    <div class="row between">
      <h3>Sistem Pengingat</h3>
      <a class="link small" href="{{ route('manajemen.jadwal.index') }}">Kelola Jadwal →</a>
    </div>
    <p class="small muted" style="margin:8px 0 14px">
      Sistem memonitor pemeliharaan mesin secara otomatis dan mengirim notifikasi WhatsApp kepada teknisi yang bertugas:
      <br>• Pengingat sebelum tanggal jatuh tempo (H-7 dan H-1)
      <br>• Pengingat saat hari-H pelaksanaan
      <br>• Peringatan keterlambatan jika melewati waktu
    </p>
    <div style="background:var(--bg);padding:10px 14px;border-radius:8px;border:1px solid var(--line);font-size:12.5px;display:flex;align-items:center;justify-content:space-between">
      <span>Notifikasi WhatsApp Gateway:</span>
      <b style="color:var(--ok)">● Aktif Terhubung</b>
    </div>
  </div>
</div>
@endsection

