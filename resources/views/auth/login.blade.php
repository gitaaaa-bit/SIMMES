@extends('layouts.auth')

@section('title', 'Masuk')
@section('heading', 'Masuk')

@section('content')
<form method="POST" action="{{ route('login.store') }}" class="card" novalidate>
  @csrf

  @if (session('status'))
    <p class="notice">{{ session('status') }}</p>
  @endif

  @if ($errors->any())
    <div class="alert" role="alert">
      @foreach ($errors->all() as $message)
        <p>{{ $message }}</p>
      @endforeach
    </div>
  @endif

  <div class="field">
    <label for="nik">NIK atau Nama Lengkap</label>
    <input id="nik" name="nik" type="text" value="{{ old('nik') }}" placeholder="Masukkan NIK atau nama lengkap"
           autocomplete="username" autofocus required>
  </div>

  <div class="field">
    <label for="password">Kata sandi</label>
    <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
  </div>

  <button type="submit" class="btn">Masuk</button>

  <p class="alt">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
</form>
@endsection