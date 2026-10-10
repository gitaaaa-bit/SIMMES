@extends('layouts.technician')
@section('title', 'Profil')
@section('heading', 'Profil')
@section('content')
<div class="tcard hero" style="text-align:center">
  <div class="avatar" style="width:74px;height:74px;font-size:26px;margin:0 auto 10px;background:var(--grad-brand);color:#fff">{{ $u->initials }}</div>
  <b style="font-size:19px">{{ $u->name }}</b><div class="muted small">NIK {{ $u->nik }} · {{ $u->department }}</div>
  <div class="tstats" style="margin:16px 0 0"><div class="tstat hot"><b>{{ $total }}</b><span>Tiket selesai</span></div><div class="tstat"><b>{{ $u->assignedTickets()->where('status','!=','selesai')->count() }}</b><span>Masih aktif</span></div></div>
</div>
<form class="tcard" method="POST" action="{{ route('teknisi.profil.update') }}">@csrf @method('PUT')
  <label class="f" style="margin-top:0">Nomor WhatsApp (untuk pengingat)</label><input class="input" name="whatsapp" value="{{ old('whatsapp', $u->whatsapp) }}">
  <label class="f">Bagian</label><input class="input" name="bagian" value="{{ old('bagian', $u->bagian) }}">
  <button class="btn primary block" style="margin-top:16px">Simpan</button>
</form>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="btn block" style="justify-content:center;color:var(--r700)">Keluar (Logout)</button></form>
@endsection
