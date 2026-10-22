@extends('layouts.manager')
@php($editing = isset($user))
@section('title', $editing ? 'Ubah Pengguna' : 'Tambah Pengguna')
@section('content')
<div class="top">
  <div>
    <h1>{{ $editing ? 'Ubah Data Pengguna' : 'Tambah Pengguna' }}</h1>
    <p>{{ $editing ? 'Perbarui penempatan, departemen, kontak, dan data kerja pengguna.' : 'NIK dan kata sandi awal otomatis dibuat jika dikosongkan.' }}</p>
  </div>
  @php
    $backRoute = (request('from') === 'teknisi' || (isset($user) && $user->role === 'teknisi')) ? route('manajemen.teknisi.index') : route('manajemen.pengguna.index');
  @endphp
  <a class="btn" href="{{ $backRoute }}">&larr; Kembali</a>
</div>

<form class="card" style="max-width:620px" method="POST" action="{{ $editing ? route('manajemen.pengguna.update', $user) : route('manajemen.pengguna.store') }}">
  @csrf
  @if ($editing) @method('PUT') @endif
  @if (request('from') || (isset($user) && $user->role === 'teknisi'))
    <input type="hidden" name="from" value="{{ request('from', 'teknisi') }}">
  @endif

  <label class="f" style="margin-top:0">Nama Lengkap</label>
  <input class="input" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Nama pengguna" required>
  @error('name')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Peran</label>
  @php($currentRole = old('role', isset($user) ? ($user->role instanceof \App\Enums\Role ? $user->role->value : $user->role) : 'teknisi'))
  <select class="select" name="role">
    <option value="teknisi" @selected($currentRole === 'teknisi')>Teknisi</option>
    <option value="manajemen" @selected($currentRole === 'manajemen')>Manager</option>
  </select>

  <label class="f">NIK <span class="muted small" style="font-weight:normal">{{ $editing ? '(wajib)' : '(opsional - otomatis jika dikosongkan)' }}</span></label>
  <input class="input" name="nik" value="{{ old('nik', $user->nik ?? '') }}" placeholder="" {{ $editing ? 'required' : '' }}>
  @error('nik')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Kata Sandi <span class="muted small" style="font-weight:normal">{{ $editing }}</span></label>
  <input class="input" type="text" name="password" value="" placeholder="Minimal 4 karakter">
  @error('password')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Email (Akun Login)</label>
  <input class="input" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="teknisi@gmail.com" required>
  @error('email')<div class="err">{{ $message }}</div>@enderror

  <label class="f">Departemen</label>
  <input class="input" name="department" value="{{ old('department', $user->department ?? 'Maintenance') }}" placeholder="Maintenance / Produksi" required>

  <label class="f">Bagian / Spesialisasi</label>
  <input class="input" name="bagian" value="{{ old('bagian', $user->bagian ?? '') }}" placeholder="">

  <label class="f">Jabatan / Posisi</label>
  <input class="input" name="job_title" value="{{ old('job_title', $user->job_title ?? '') }}" placeholder="">

  <label class="f">Lokasi Penempatan Kerja</label>
  <input class="input" name="work_location" value="{{ old('work_location', $user->work_location ?? '') }}" placeholder="">
  <div class="tiny muted" style="margin-top:4px"></div>

  <label class="f">Nomor WhatsApp</label>
  <input class="input" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? '') }}" placeholder="Contoh: 081234567890 atau 6281234567890">
  <div class="tiny muted" style="margin-top:4px">Nomor WhatsApp teknisi untuk menerima broadcast notifikasi perbaikan mesin & tugas secara otomatis.</div>

  <button class="btn primary block" style="margin-top:22px">{{ $editing ? 'Simpan Perubahan' : 'Simpan Pengguna' }}</button>
</form>
@endsection
