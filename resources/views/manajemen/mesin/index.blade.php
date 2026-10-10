@extends('layouts.manager')
@section('title', 'Data Mesin')
@section('content')
<div class="top">
  <div><h1>Data Mesin</h1></div>
  <div class="actions">
    <form id="imp" method="POST" action="{{ route('manajemen.mesin.import') }}" enctype="multipart/form-data">@csrf
      <input type="file" name="file" accept=".csv,text/csv" hidden onchange="this.form.submit()">
      <button type="button" class="btn" onclick="this.previousElementSibling.click()" title="Format CSV: kode,nama,kategori,tipe,serial,tgl_beli,lokasi,interval_bulan">Import Excel (CSV)</button>
    </form>
    <a class="btn primary" href="{{ route('manajemen.mesin.create') }}">@include('partials.icon', ['n' => 'plus']) Tambah Mesin</a>
  </div>
</div>
<form class="filters" method="GET">
  <input class="input" name="q" value="{{ request('q') }}" placeholder="Cari mesin">
  <select class="select" name="location" onchange="this.form.submit()">
    <option value="">Semua lokasi</option>
    @foreach ($locations as $l)<option @selected(request('location') === $l)>{{ $l }}</option>@endforeach
  </select>
  <select class="select" name="status" onchange="this.form.submit()">
    <option value="">Semua status</option>
    @foreach (['aktif','dilaporkan','diproses','nonaktif'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
  </select>
</form>
<div class="card tablewrap">
  <table>
    <thead><tr><th>Kode</th><th>Nama mesin</th><th>Lokasi</th><th>Servis terakhir</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse ($machines as $m)
      <tr>
        <td><b>{{ $m->code }}</b></td><td>{{ $m->name }}</td><td>{{ $m->location }}</td>
        <td>{{ $m->last_service_at?->translatedFormat('d M Y') ?? '-' }}</td>
        <td>@include('partials.chip', ['v' => $m->status])</td>
        <td class="row">
          <a class="link" href="{{ route('manajemen.mesin.edit', $m) }}">edit</a>
          <a class="link" href="{{ route('manajemen.mesin.show', $m) }}">detail</a>
          <form method="POST" action="{{ route('manajemen.mesin.destroy', $m) }}" onsubmit="return confirm('Hapus {{ $m->code }}?')">@csrf @method('DELETE')<button class="link danger">hapus</button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6" class="empty">Belum ada mesin.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="pagination">{{ $machines->links() }}</div>
@endsection
