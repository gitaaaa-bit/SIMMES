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
        <td>
          @if ($t->technician)
            <b>{{ $t->technician->name }}</b>
          @else
            <span class="chip" style="background:#ffedd5;color:#c2410c;font-size:11px;font-weight:800">⚡ Belum Diambil</span>
          @endif
        </td>
        <td>
          <a class="link" href="{{ route('manajemen.perbaikan.show', $t) }}">Detail</a>
          @if ($t->technician?->whatsapp)
            @php
              $msg = \App\Services\WhatsAppService::buildTicketMessage($t, $t->technician);
              $wa = \App\Services\WhatsAppService::makeDirectLink($t->technician->whatsapp, $msg);
            @endphp
            @if ($wa)
              <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;margin-left:8px;color:#16a34a" title="Hubungi WA {{ $t->technician->name }}">
                <svg class="i" style="width:17px;height:17px" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
              </a>
            @endif
          @elseif ($t->technician_id === null)
            @php
              $bc = \App\Services\WhatsAppService::makeBroadcastShareLink($t);
            @endphp
            <a href="{{ $bc }}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;margin-left:8px;color:#16a34a" title="Siarkan ke WhatsApp Grup Teknisi">
              <svg class="i" style="width:17px;height:17px" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            </a>
          @endif
        </td>
    @empty
      <tr><td colspan="7" class="empty">Tidak ada tiket yang cocok.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="pagination">{{ $tickets->links() }}</div>
@endsection
