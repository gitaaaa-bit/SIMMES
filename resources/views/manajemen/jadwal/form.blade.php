@extends('layouts.manager')
@section('title', 'Tambah Jadwal')
@section('content')
<div class="top"><div><h1>Tambah Jadwal</h1></div><a class="btn" href="{{ route('manajemen.jadwal.index') }}">← Kembali</a></div>
<form class="card" style="max-width:720px" method="POST" action="{{ route('manajemen.jadwal.store') }}">@csrf
  <label class="f" style="margin-top:0">Mesin</label>
  <select class="select" name="machine_id" required>@foreach ($machines as $m)<option value="{{ $m->id }}" @selected(old('machine_id') == $m->id)>{{ $m->label }}</option>@endforeach</select>
  <div class="grid g2">
    <div><label class="f">Jenis perawatan</label><input class="input" name="task" value="{{ old('task') }}" placeholder="Ganti oli" required></div>
    <div><label class="f">Tipe</label><select class="select" name="type"><option value="rutin">Rutin</option><option value="insiden">Insiden</option></select></div>
    <div><label class="f">Interval (bulan)</label><input class="input" type="number" min="1" name="interval_months" value="{{ old('interval_months', 3) }}"></div>
    <div><label class="f">Jatuh tempo</label><input class="input" type="date" name="due_date" value="{{ old('due_date') }}" required></div>
  </div>
  <label class="f">Teknisi (opsional)</label>
  <select class="select" name="technician_id"><option value="">Belum ditugaskan</option>
    @foreach (\App\Models\User::where('role','teknisi')->where('status','aktif')->get() as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select>
  <button class="btn primary block" style="margin-top:22px">Simpan Jadwal</button>
</form>
@endsection
