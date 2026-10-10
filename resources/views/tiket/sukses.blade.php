@extends(auth()->user()->isTeknisi() ? 'layouts.technician' : 'layouts.manager')
@section('title', 'Laporan terkirim')
@section('heading', 'Laporan Perbaikan')
@section('nonav', '1')
@section('content')
@php $role = auth()->user()->role; @endphp
<div style="max-width:460px;margin:0 auto;text-align:center">
  <div class="okbig">@include('partials.icon', ['n' => 'check'])</div>
  <h2 style="font-size:26px;font-weight:800">Laporan terkirim</h2>
  <p class="muted" style="margin-bottom:22px">Teknisi akan segera melihat laporanmu.</p>
  <div class="card" style="text-align:left;padding:18px 22px;margin-bottom:18px">
    <div class="row between"><span class="muted">Nomor tiket</span><b>{{ $ticket->code }}</b></div>
    <div class="row between" style="margin:8px 0"><span class="muted">Mesin</span><b>{{ $ticket->machine->label }}</b></div>
    <div class="row between"><span class="muted">Status</span>@include('partials.chip', ['v' => $ticket->status])</div>
  </div>
  <a class="btn primary block" href="{{ route($role.'.lapor') }}">Lapor Lagi</a>
  <a class="btn block" style="margin-top:10px;border-color:var(--r400);color:var(--r700)" href="{{ route($role === 'teknisi' ? 'teknisi.home' : 'manajemen.dashboard') }}">Keluar</a>
</div>
@endsection
