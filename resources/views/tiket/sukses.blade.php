@extends(auth()->user()->isTeknisi() ? 'layouts.technician' : 'layouts.manager')
@section('title', 'Laporan terkirim')
@section('heading', 'Laporan Perbaikan')
@section('nonav', '1')
@section('content')
@php
  $role = auth()->user()->role;
  $role = $role instanceof \BackedEnum ? $role->value : $role;
@endphp
<div style="max-width:460px;margin:0 auto;text-align:center">
  <div class="okbig">@include('partials.icon', ['n' => 'check'])</div>
  <h2 style="font-size:26px;font-weight:800">Laporan terkirim</h2>
  <p class="muted" style="margin-bottom:22px">Teknisi akan segera melihat laporanmu.</p>
  <div class="card" style="text-align:left;padding:18px 22px;margin-bottom:18px">
    <div class="row between"><span class="muted">Nomor tiket</span><b>{{ $ticket->code }}</b></div>
    <div class="row between" style="margin:8px 0"><span class="muted">Mesin</span><b>{{ $ticket->machine->label }}</b></div>
    <div class="row between"><span class="muted">Status</span>@include('partials.chip', ['v' => $ticket->status])</div>
  </div>
  @if (!empty($broadcastLink))
    <a href="{{ $broadcastLink }}" target="_blank" rel="noopener noreferrer" class="btn block" style="margin-bottom:12px;background:#16a34a;color:#fff;border-color:transparent;font-weight:700;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 8px 18px -4px rgba(22,163,74,0.4)">
      <svg class="i" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
      Siarkan ke WhatsApp Grup Teknisi
    </a>
  @endif
  <a class="btn primary block" href="{{ route($role.'.lapor') }}">Lapor Lagi</a>
  <a class="btn block" style="margin-top:10px;border-color:var(--r400);color:var(--r700)" href="{{ route($role === 'teknisi' ? 'teknisi.home' : 'manajemen.dashboard') }}">Keluar</a>
</div>
@endsection