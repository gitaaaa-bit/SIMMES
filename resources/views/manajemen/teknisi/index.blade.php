@extends('layouts.manager')
@section('title', 'Teknisi')
@section('content')
<div class="top"><div><h1>Teknisi</h1><p>Beban kerja tiap teknisi.</p></div>
  <a class="btn primary" href="{{ route('manajemen.pengguna.create') }}">@include('partials.icon', ['n' => 'plus']) Tambah Pengguna</a></div>
<div class="grid g3">
  @forelse ($technicians as $t)
    <a class="card" href="{{ route('manajemen.teknisi.show', $t) }}" style="display:block">
      <div class="person"><div class="avatar">{{ $t->initials }}</div><div><b>{{ $t->name }}</b><div class="small muted">NIK {{ $t->nik }} · {{ $t->department }}</div></div></div>
      <div class="grid g2" style="margin-top:18px;gap:10px">
        <div class="stat" style="padding:12px 16px"><span>Aktif</span><b style="font-size:28px">{{ $t->active_count }}</b></div>
        <div class="stat s2" style="padding:12px 16px"><span>Selesai</span><b style="font-size:28px">{{ $t->done_count }}</b></div>
      </div>
      <div class="tiny" style="margin-top:12px">WA: {{ $t->whatsapp ?? '-' }}</div>
    </a>
  @empty <div class="card empty">Belum ada teknisi aktif.</div> @endforelse
</div>
@endsection
