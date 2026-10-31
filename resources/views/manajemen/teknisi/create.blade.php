@extends('layouts.manager')
@section('title', 'Tambah Teknisi')
@section('content')
<div class="top">
  <div class="top-row">
    <h1>Tambah Teknisi</h1>
    <a class="btn" href="{{ route('manajemen.teknisi.index') }}" style="background:#fff">← Kembali</a>
  </div>
  <p class="muted small">Lengkapi formulir di bawah untuk mendaftarkan akun teknisi baru.</p>
</div>

<form class="card" style="max-width:620px" method="POST" action="{{ route('manajemen.teknisi.store') }}">
  @csrf

  <label class="f" style="margin-top:0">Nama Lengkap <span style="color:#ef4444">*</span></label>
  <input class="input" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
  @error('name')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Email (Akun Login) <span style="color:#ef4444">*</span></label>
  <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: teknisi@perusahaan.com" required>
  <div class="tiny muted" style="margin-top:4px">Digunakan oleh teknisi untuk masuk/login ke dalam sistem.</div>
  @error('email')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Nomor Induk Karyawan (NIK) <span style="color:#ef4444">*</span></label>
  <input class="input" id="nikInput" name="nik" value="{{ old('nik', $suggestedNik) }}" placeholder="Contoh: TEK001" required>
  <div class="tiny muted" style="margin-top:4px">Nomor identitas unik teknisi di sistem SIMMES.</div>
  @error('nik')<div class="err">{{ $message }}</div>@enderror

  <div class="row between" style="margin:16px 0 6px">
    <label class="f" style="margin:0">Kata Sandi (Password) <span class="muted small">(Opsional)</span></label>
    <button type="button" class="link small" onclick="useNikAsPassword()" style="font-size:11.5px">Samakan dengan NIK</button>
  </div>
  <div style="position:relative">
    <input class="input" id="passInput" type="text" name="password" value="{{ old('password') }}" placeholder="Default sama dengan NIK jika dikosongkan" style="padding-right:90px">
    <button type="button" onclick="togglePassVis()" class="btn-action" style="position:absolute;right:8px;top:8px;padding:4px 8px;font-size:11px" id="btnToggleVis">
      Sembunyi
    </button>
  </div>
  <div class="tiny muted" style="margin-top:4px">Jika dikosongkan, kata sandi awal otomatis sama dengan NIK.</div>
  @error('password')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Nomor WhatsApp</label>
  <input class="input" type="tel" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Contoh: 081234567890">
  <div class="tiny muted" style="margin-top:4px">Digunakan untuk menerima notifikasi penugasan & jadwal maintenance otomatis.</div>
  @error('whatsapp')<div class="err">{{ $message }}</div>@enderror

  <div class="row" style="gap:14px;margin-top:16px">
    <div style="flex:1">
      <label class="f" style="margin-top:0">Departemen</label>
      <input class="input" name="department" value="{{ old('department', 'Maintenance') }}" placeholder="Maintenance" required>
    </div>
    <div style="flex:1">
      <label class="f" style="margin-top:0">Bagian / Spesialisasi</label>
      <input class="input" name="bagian" value="{{ old('bagian') }}" placeholder="Mekanik / Listrik / CNC" list="spesialisasiList">
      <datalist id="spesialisasiList">
        <option value="Mekanik">
        <option value="Kelistrikan & Otomasi">
        <option value="Mesin CNC / Bubut">
        <option value="Hydraulic & Pneumatic">
        <option value="Preventive Maintenance">
      </datalist>
    </div>
  </div>

  <div class="form-actions-end" style="margin-top:24px">
    <button type="submit" class="btn primary btn-compact">Simpan Teknisi</button>
  </div>
</form>

<script>
  function useNikAsPassword() {
    const nik = document.getElementById('nikInput').value.trim();
    if (nik) {
      document.getElementById('passInput').value = nik;
    }
  }

  function togglePassVis() {
    const input = document.getElementById('passInput');
    const btn = document.getElementById('btnToggleVis');
    if (input.type === 'password') {
      input.type = 'text';
      btn.textContent = 'Sembunyi';
    } else {
      input.type = 'password';
      btn.textContent = 'Lihat';
    }
  }
</script>
@endsection
