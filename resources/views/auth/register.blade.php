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