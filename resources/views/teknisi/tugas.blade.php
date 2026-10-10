@extends('layouts.technician')
@section('title', 'Tugas Teknisi')
@section('heading', 'Tugas Teknisi')
@section('sub', $tickets->count().' tugas')
@section('content')
<form method="GET" class="tcard" style="padding:10px 14px">
  <input type="hidden" name="f" value="{{ $filter }}">
  <input class="input" style="border:0;padding:6px" name="q" value="{{ request('q') }}" placeholder="Cari Kode atau mesin">
</form>
<div class="pills">
  @foreach (['semua' => 'Semua', 'belum' => 'Belum Diambil', 'proses' => 'Dikerjakan', 'selesai' => 'Selesai'] as $k => $l)
    <a href="{{ route('teknisi.tugas.index', ['f' => $k]) }}" class="{{ $filter === $k ? 'on' : '' }}">{{ $l }}</a>
  @endforeach
</div>
@forelse ($tickets as $t)
  <div class="tcard task {{ $t->urgency }}">
    <div class="t1"><a href="{{ route('teknisi.tugas.show', $t) }}">{{ $t->code }} · {{ $t->machine->code }}</a>
      @include('partials.chip', ['v' => $t->display_status, 'label' => in_array($t->status, ['ditugaskan','dilaporkan']) && !$t->is_overdue ? 'Belum Diambil' : null])</div>
    <p><b>{{ $t->machine->name }}, {{ $t->machine->location }}</b><br><span class="muted small">{{ $t->complaint }}</span></p>
    <div style="margin-bottom:12px">@include('partials.chip', ['v' => $t->urgency])</div>
    @if (in_array($t->status, ['ditugaskan', 'dilaporkan']))
      <form method="POST" action="{{ route('teknisi.tugas.start', $t) }}">@csrf<button class="btn primary block">Mulai Proses</button></form>
    @else
      <a class="btn block" style="justify-content:center" href="{{ route('teknisi.tugas.show', $t) }}">Buka Detail</a>
    @endif
  </div>
@empty
  <div class="tcard empty">Tidak ada tugas pada filter ini.</div>
@endforelse
@endsection
