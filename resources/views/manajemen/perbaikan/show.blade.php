@extends('layouts.manager')
@section('title', $ticket->code)
@section('content')
<div class="top">
  <div><h1>{{ $ticket->code }}</h1><p>{{ $ticket->machine->label }} · {{ $ticket->machine->location }}</p></div>
  <div class="actions">@include('partials.chip', ['v' => $ticket->display_status])<a class="btn" href="{{ route('manajemen.perbaikan.index') }}">← Kembali</a></div>
</div>

<div class="grid g2">
  <div class="card">
    <h3>Informasi</h3>
    <div class="kv" style="grid-template-columns:1fr 1fr">
      <div><span>Keluhan</span><b>{{ $ticket->complaint }}</b></div>
      <div><span>Urgensi</span>@include('partials.chip', ['v' => $ticket->urgency])</div>
      <div><span>Pelapor</span><b>{{ $ticket->reporter?->name ?? '-' }}</b></div>
      <div><span>Batas waktu</span><b>{{ $ticket->due_at?->translatedFormat('d M Y H:i') ?? '-' }}</b></div>
      <div><span>Tindakan</span><b>{{ $ticket->action ?? '-' }}</b></div>
      <div><span>Sparepart</span><b>{{ $ticket->spareparts ?? '-' }}</b></div>
    </div>
    <div style="margin-top:20px"><span class="tiny">Progress pekerjaan · {{ $ticket->progress }}%</span>
      <div class="progress"><i style="width:{{ $ticket->progress }}%"></i></div></div>
  </div>

  <div class="card">
    <h3>Penugasan teknisi</h3>
    <form method="POST" action="{{ route('manajemen.perbaikan.assign', $ticket) }}">@csrf
      <label class="f" style="margin-top:0">Teknisi</label>
      <select class="select" name="technician_id" required>
        <option value="">Pilih teknisi</option>
        @foreach ($technicians as $t)<option value="{{ $t->id }}" @selected($ticket->technician_id === $t->id)>{{ $t->name }}</option>@endforeach
      </select>
      <label class="f">Batas waktu</label>
      <input class="input" type="datetime-local" name="due_at" value="{{ $ticket->due_at?->format('Y-m-d\TH:i') }}">
      <button class="btn primary block" style="margin-top:18px">Tugaskan</button>
    </form>
  </div>
</div>

<div class="grid g2" style="margin-top:18px">
  <div class="card">
    <h3>Foto / video</h3>
    @foreach (['report' => 'Laporan awal', 'before' => 'Sebelum', 'after' => 'Sesudah'] as $ph => $lbl)
      <div class="small muted" style="margin:10px 0 6px">{{ $lbl }}</div>
      <div class="row wrap">
        @forelse ($ticket->mediaOf($ph) as $m)
          <div class="thumb">@if ($m->kind === 'video')<video src="{{ $m->url }}" controls></video>@else<img src="{{ $m->url }}" alt="">@endif</div>
        @empty<span class="tiny">{{ $ph === 'after' ? 'Belum diunggah teknisi.' : 'Belum ada.' }}</span>@endforelse
      </div>
    @endforeach
  </div>
  <div class="card">
    <h3>Riwayat status</h3>
    <ul class="tl">
      @foreach ($ticket->logs as $l)
        <li><b>{{ $l->label }}</b><div class="tiny">{{ $l->created_at->translatedFormat('d M Y, H:i') }}{{ $l->note ? ' · '.$l->note : '' }}</div></li>
      @endforeach
    </ul>
  </div>
</div>
@endsection
