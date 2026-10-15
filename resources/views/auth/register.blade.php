@extends('layouts.auth')

@section('title', 'Daftar')
@section('heading', 'Daftar')

@section('content')
<form method="POST" action="{{ route('register.store') }}" class="card" novalidate>
  @csrf

  @if ($errors->any())
    <div class="alert" role="alert">
      @foreach ($errors->all() as $message)
        <p>{{ $message }}</p>
      @endforeach
    </div>
  @endif

  <div class="field">
    <label for="name">Nama lengkap</label>
    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" autocomplete="name" autofocus required>
  </div>

  <div class="field">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Contoh: budi@gmail.com" autocomplete="email" required>
  </div>

  <div class="field">
    <label for="whatsapp">Nomor WhatsApp</label>
    <input id="whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}" placeholder="Contoh: 081234567890" autocomplete="tel" required>
    <small style="color:var(--ink2,#64748b);font-size:12px;margin-top:4px;display:block">
      Nomor ini terhubung untuk notifikasi penugasan perbaikan & jadwal maintenance otomatis.
    </small>
  </div>

  <div class="field">
    <label for="role">Peran</label>
    <select id="role" name="role" required>
      <option value="" disabled @selected(! old('role'))>Pilih peran</option>
      @foreach ($roles as $role)
        <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
      @endforeach
    </select>
  </div>

  <div class="field">
    <label for="password">Kata sandi</label>
    <input id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
  </div>

  <div class="field">
    <label for="password_confirmation">Konfirmasi kata sandi</label>
    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ketik ulang kata sandi" autocomplete="new-password" required>
  </div>

  <button type="submit" class="btn">Daftar Sekarang</button>

  <p class="alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</form>
@endsection