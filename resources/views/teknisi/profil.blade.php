@extends('layouts.technician')
@section('title', 'Profil')
@section('heading', 'Profil')
@section('content')
<div class="tcard hero" style="text-align:center">
  <div class="avatar" style="width:74px;height:74px;font-size:26px;margin:0 auto 10px;background:var(--grad-brand);color:#fff">{{ $u->initials }}</div>
  <b style="font-size:19px">{{ $u->name }}</b>
  <div class="muted small">NIK {{ $u->nik }} · {{ $u->department }}</div>
  <div class="tstats" style="margin:16px 0 0">
    <div class="tstat hot"><b>{{ $total }}</b><span>Tiket selesai</span></div>
    <div class="tstat"><b>{{ $u->assignedTickets()->where('status','!=','selesai')->count() }}</b><span>Masih aktif</span></div>
  </div>
</div>

<div class="tcard">
  <h3 style="font-size:16px;font-weight:800;margin-bottom:6px">Notifikasi WhatsApp</h3>
  <p class="muted small" style="margin-bottom:14px;line-height:1.4">
    Nomor WhatsApp ini digunakan sistem SIMMES untuk mengirimkan notifikasi penugasan tiket perbaikan dan pengingat jadwal pemeliharaan secara otomatis.
  </p>

  <form method="POST" action="{{ route('teknisi.profil.update') }}">
    @csrf
    @method('PUT')

    <label class="f" style="margin-top:0">Nama Pengguna</label>
    <input class="input" name="name" type="text" value="{{ old('name', $u->name) }}" placeholder="" required>
    @error('name')<div class="err">{{ $message }}</div>@enderror

    <label class="f" style="margin-top:14px">Nomor WhatsApp Aktif</label>
    <div style="position:relative">
      <input class="input" name="whatsapp" type="tel" value="{{ old('whatsapp', $u->whatsapp) }}" placeholder="" required>
    </div>
    <div class="tiny" style="margin-top:4px;color:var(--ink3)">Gunakan format nomor lokal (08...) atau internasional (628...).</div>

    <label class="f" style="margin-top:14px">Bagian / Unit Kerja</label>
    <input class="input" name="bagian" value="{{ old('bagian', $u->bagian) }}" placeholder="">

    <button type="submit" class="btn primary block" style="margin-top:16px">Simpan Nama & Profil</button>
  </form>

<form method="POST" action="{{ route('logout') }}" style="margin-top:16px">
  @csrf
  <button class="btn block" style="justify-content:center;color:var(--r700)">Keluar (Logout)</button>
</form>

<script>
function testSystemAlert() {
  if (window.Notification) {
    Notification.requestPermission().then(perm => {
      if (perm === 'granted') {
        try {
          new Notification("⚡ SIMMES: Notifikasi Layar Ponsel", {
            body: "Notifikasi perbaikan mesin baru di layar ponsel Anda telah aktif!",
            icon: "/favicon.ico"
          });
        } catch(e) {}
      }
    });
  }

  try {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (AudioContext) {
      const ctx = new AudioContext();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(659.25, ctx.currentTime);
      gain.gain.setValueAtTime(0.2, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.35);
    }
  } catch(e) {}
}
</script>
@endsection
