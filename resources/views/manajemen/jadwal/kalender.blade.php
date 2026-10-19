@extends('layouts.manager')
@section('title', 'Kalender Maintenance')
@section('content')
<div class="top"><div><h1>Jadwal Maintenance</h1></div>
  <a class="btn primary" href="{{ route('manajemen.jadwal.create') }}">@include('partials.icon', ['n' => 'plus']) Tambah Jadwal</a></div>
@include('manajemen.jadwal._tabs')
@php
  $lead = $month->dayOfWeekIso - 1;           // kolom kosong sebelum tanggal 1 (Senin = 0)
  $days = $month->daysInMonth;
  $cells = ceil(($lead + $days) / 7) * 7;
  $tone = ['selesai' => 'c-selesai', 'terlambat' => 'c-terlambat', 'terjadwal' => 'c-sedang', 'dikerjakan' => 'c-sedang'];
@endphp
<div class="layout-cal">
  <div class="card">
    <div class="row between" style="margin-bottom:14px">
      <div class="row">
        <a class="btn" href="{{ route('manajemen.jadwal.kalender', ['bulan' => $month->copy()->subMonth()->format('Y-m')]) }}">←</a>
        <a class="btn" href="{{ route('manajemen.jadwal.kalender', ['bulan' => $month->copy()->addMonth()->format('Y-m')]) }}">→</a>
      </div>
      <h2 style="margin:0">{{ $month->translatedFormat('F Y') }}</h2>
      <span class="small muted">Hari ini: {{ now()->translatedFormat('j M') }}</span>
    </div>
    <div class="cal">
      @foreach (['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)<div class="h">{{ $h }}</div>@endforeach
      @for ($i = 0; $i < $cells; $i++)
        @php $d = $i - $lead + 1; $ok = $d >= 1 && $d <= $days; @endphp
        <div class="d {{ !$ok ? 'off' : '' }} {{ $ok && $month->copy()->day($d)->isToday() ? 'today' : '' }}">
          @if ($ok)
            <span>{{ $d }}</span>
            @foreach ($byDay[$d] ?? [] as $e)
              <a class="ev {{ $tone[$e->display_status] ?? 'c-sedang' }}" href="{{ route('manajemen.mesin.show', [$e->machine, 'from' => 'kalender']) }}">{{ $e->task }} {{ $e->machine->code }}</a>
            @endforeach
          @endif
        </div>
      @endfor
    </div>
  </div>
  <div>
    <div class="card" style="margin-bottom:18px">
      <h3>Notifikasi Bulan Ini</h3>
      @forelse ($items as $e)
        @php $dd = (int) now()->startOfDay()->diffInDays($e->due_date, false); @endphp
        <div class="row between" style="padding:10px 0;border-top:1px solid var(--line)">
          <div><b class="small">{{ $e->due_date->translatedFormat('j M') }} · {{ $e->task }} · {{ $e->machine->code }}</b>
            <div class="tiny">{{ $e->status === 'selesai' ? 'Selesai dikerjakan' : ($dd < 0 ? 'Terlambat '.abs($dd).' hari' : ($dd <= 7 ? 'Pengingat H-'.$dd.' aktif' : 'Pengingat H-7 dijadwalkan '.$e->due_date->copy()->subDays(7)->translatedFormat('j M'))) }}</div></div>
          @include('partials.chip', ['v' => $e->display_status])
        </div>
      @empty <div class="empty">Tidak ada jadwal bulan ini.</div> @endforelse
    </div>
    <div class="card">
      <h3>Aturan notifikasi</h3>
      <div class="muted">H-7 dan H-1: teknisi (WA/email)<br>Hari-H: teknisi<br>Terlambat: teknisi dan manager</div>
    </div>
  </div>
</div>
@endsection
