@extends('layouts.manager')
@section('title', $machine->code)
@section('content')
@php
  $from = request('from');
  $backUrl = route('manajemen.mesin.index');

  if ($from === 'dashboard') {
      $backUrl = route('manajemen.dashboard');
  } elseif ($from === 'jadwal') {
      $backUrl = route('manajemen.jadwal.index');
  } elseif ($from === 'kalender') {
      $backUrl = route('manajemen.jadwal.kalender');
  } else {
      $prev = url()->previous();
      if ($prev && $prev !== url()->current()) {
          $dashPath = rtrim(parse_url(route('manajemen.dashboard'), PHP_URL_PATH) ?? '', '/');
          $prevPath = rtrim(parse_url($prev, PHP_URL_PATH) ?? '', '/');
          if ($dashPath && $prevPath === $dashPath) {
              $backUrl = route('manajemen.dashboard');
          } elseif ($prevPath === rtrim(parse_url(route('manajemen.jadwal.index'), PHP_URL_PATH) ?? '', '/')) {
              $backUrl = route('manajemen.jadwal.index');
          } elseif ($prevPath === rtrim(parse_url(route('manajemen.jadwal.kalender'), PHP_URL_PATH) ?? '', '/')) {
              $backUrl = route('manajemen.jadwal.kalender');
          }
      }
  }
@endphp
<div class="top">
  <div><h1>Detail Mesin</h1><p>{{ $machine->label }}</p></div>
  <div class="actions">
    @if ($machine->needs_service)<span class="chip c-diproses">Perlu servis</span>@endif
    <a class="btn" href="{{ $backUrl }}">← Kembali</a>
  </div>
</div>
<div class="card">
  <div class="kv">
    <div><span>Kategori</span><b>{{ $machine->category ?? '-' }}</b></div>
    <div><span>Tipe / model</span><b>{{ $machine->model ?? '-' }}</b></div>
    <div><span>Nomor seri</span><b>{{ $machine->serial_number ?? '-' }}</b></div>
    <div><span>Lokasi</span><b>{{ $machine->location }}</b></div>
    <div><span>Tanggal pembelian</span><b>{{ $machine->purchase_date?->translatedFormat('d M Y') ?? '-' }}</b></div>
    <div><span>Jadwal berikutnya</span><b style="color:var(--r600)">{{ $machine->nextSchedule?->due_date->translatedFormat('d M Y') ?? '-' }}</b></div>
  </div>
  <div class="small muted" style="margin:22px 0 8px">Foto / video awal</div>
  <div class="row wrap">
    @forelse ($machine->media ?? [] as $p)
      <div class="thumb">@if (preg_match('/\.(mp4|mov)$/i', $p))<video src="{{ Storage::url($p) }}" controls></video>@else<img src="{{ Storage::url($p) }}" alt="">@endif</div>
    @empty <span class="tiny">Belum ada dokumentasi.</span> @endforelse
  </div>
</div>
<h3 style="margin:26px 0 12px;font-size:18px">Riwayat Pekerjaan (sebelum dan sesudah)</h3>
<div class="card tablewrap">
  <table>
    <thead><tr><th>Tanggal</th><th>Jenis</th><th>Foto sebelum</th><th>Foto sesudah</th><th>Status</th></tr></thead>
    <tbody>
    @forelse ($machine->tickets as $t)
      @php $b = $t->mediaOf('before')->first(); $a = $t->mediaOf('after')->first(); @endphp
      <tr>
        <td>{{ $t->created_at->translatedFormat('d M Y') }}</td><td>{{ ucfirst($t->type) }}</td>
        <td><div class="thumb" style="width:74px;height:56px">@if ($b)<img src="{{ $b->url }}" alt="">@else Sebelum @endif</div></td>
        <td>@if ($a)<div class="thumb" style="width:74px;height:56px"><img src="{{ $a->url }}" alt=""></div>@else<span class="muted">Belum ada</span>@endif</td>
        <td>@include('partials.chip', ['v' => $t->display_status])</td>
      </tr>
    @empty <tr><td colspan="5" class="empty">Belum ada riwayat.</td></tr> @endforelse
    </tbody>
  </table>
</div>
@endsection
