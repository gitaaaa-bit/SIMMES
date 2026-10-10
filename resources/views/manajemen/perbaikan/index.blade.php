@extends('layouts.manager')
@section('title', 'Perbaikan')
@section('content')
<div class="top">
  <div><h1>Perbaikan</h1></div>
  <a class="btn primary" href="{{ route('manajemen.lapor') }}">@include('partials.icon', ['n' => 'plus']) Lapor</a>
</div>
<form class="filters" method="GET">
  <input class="input" name="q" value="{{ request('q') }}" placeholder="Cari kode tiket atau mesin">
  <select class="select" name="status" onchange="this.form.submit()">
    <option value="">Semua status</option>
    @foreach (['dilaporkan','ditugaskan','diproses','selesai','terlambat'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
  </select>
  <select class="select" name="urgency" onchange="this.form.submit()">
    <option value="">Semua urgensi</option>
    @foreach (['tinggi','sedang','rendah'] as $s)<option value="{{ $s }}" @selected(request('urgency') === $s)>{{ ucfirst($s) }}</option>@endforeach
  </select>
  <select class="select" name="location" onchange="this.form.submit()">
    <option value="">Semua lokasi</option>
    @foreach ($locations as $l)<option @selected(request('location') === $l)>{{ $l }}</option>@endforeach
  </select>
</form>
<div class="card tablewrap">
  <table>
    <thead><tr><th>Kode/Tipe</th><th>Mesin</th><th>Dilaporkan</th><th>Urgensi</th><th>Teknisi</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($tickets as $t)
      <tr>
        <td><b>{{ $t->code }}</b><div class="tiny">{{ ucfirst($t->type) }}</div></td>
        <td>{{ $t->machine->label }}</td>
        <td>{{ $t->created_at->translatedFormat('d M Y') }}</td>
        <td>@include('partials.chip', ['v' => $t->urgency])</td>
        <td>{{ $t->technician?->name ?? 'Belum ditugaskan' }}</td>
        <td>@include('partials.chip', ['v' => $t->display_status])</td>
        <td><a class="link" href="{{ route('manajemen.perbaikan.show', $t) }}">Detail</a></td>
      </tr>
    @empty
      <tr><td colspan="7" class="empty">Tidak ada tiket yang cocok.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="pagination">{{ $tickets->links() }}</div>
@endsection
