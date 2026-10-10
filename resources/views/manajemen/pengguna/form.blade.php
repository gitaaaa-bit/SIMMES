@extends('layouts.manager')
@section('title', 'Tambah Pengguna')
@section('content')
<div class="top"><div><h1>Tambah Pengguna</h1><p>Kata sandi awal sama dengan NIK.</p></div><a class="btn" href="{{ route('manajemen.pengguna.index') }}">← Kembali</a></div>
<form class="card" style="max-width:620px" method="POST" action="{{ route('manajemen.pengguna.store') }}">@csrf
  <label class="f" style="margin-top:0">Nama</label><input class="input" name="name" value="{{ old('name') }}" required>
  <label class="f">NIK</label><input class="input" name="nik" value="{{ old('nik') }}" required>
  <label class="f">Departemen</label><input class="input" name="department" value="{{ old('department') }}" required>
  <label class="f">Bagian</label><input class="input" name="bagian" value="{{ old('bagian') }}">
  <label class="f">Nomor WhatsApp</label><input class="input" name="whatsapp" value="{{ old('whatsapp') }}">
  <label class="f">Peran</label><select class="select" name="role"><option value="teknisi">Teknisi</option><option value="manajemen">Manager</option></select>
  <button class="btn primary block" style="margin-top:22px">Simpan</button>
</form>
@endsection
