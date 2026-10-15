@extends('layouts.manager')
@section('title', 'Tambah Pengguna')
@section('content')
<div class="top">
  <div>
    <h1>Tambah Pengguna</h1>
    <p>NIK dan kata sandi awal otomatis di-generate oleh sistem jika dikosongkan.</p>
  </div>
  <a class="btn" href="{{ route('manajemen.pengguna.index') }}">← Kembali</a>
</div>

<form class="card" style="max-width:620px" method="POST" action="{{ route('manajemen.pengguna.store') }}">
  @csrf
  <label class="f" style="margin-top:0">Nama Lengkap</label>
  <input class="input" name="name" value="{{ old('name') }}" placeholder="Nama pengguna" required>

  <label class="f">Peran (Role)</label>
  <select class="select" name="role">
    <option value="teknisi" @selected(old('role') == 'teknisi')>Teknisi</option>
    <option value="manajemen" @selected(old('role') == 'manajemen')>Manager</option>
  </select>

  <label class="f">NIK <span class="muted small" style="font-weight:normal">(Opsional — Otomatis jika dikosongkan)</span></label>
  <input class="input" name="nik" value="{{ old('nik') }}" placeholder="Biarkan kosong untuk generate otomatis (misal: TEK003 / MGR003)">
  <div class="tiny muted" style="margin-top:4px">Jika dikosongkan, sistem akan otomatis membuat NIK berurutan dan kata sandi awal akan disamakan dengan NIK.</div>

  <label class="f">Email <span class="muted small" style="font-weight:normal">(Opsional)</span></label>
  <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: teknisi@perusahaan.com (Otomatis jika kosong)">
  <div class="tiny muted" style="margin-top:4px">Digunakan untuk login ke sistem. Jika kosong, sistem otomatis membuat email default dari NIK.</div>

  <label class="f">Departemen</label>
  <input class="input" name="department" value="{{ old('department', 'Maintenance') }}" placeholder="Maintenance / Produksi" required>

  <label class="f">Bagian / Spesialisasi</label>
  <input class="input" name="bagian" value="{{ old('bagian') }}" placeholder="Mekanik / Listrik / Mesin CNC (opsional)">

  <label class="f">Nomor WhatsApp</label>
  <input class="input" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Contoh: 081234567890">
  <div class="tiny muted" style="margin-top:4px">Digunakan untuk menerima notifikasi tugas & jadwal maintenance otomatis.</div>

  <button class="btn primary block" style="margin-top:22px">Simpan Pengguna</button>
</form>
@endsection

