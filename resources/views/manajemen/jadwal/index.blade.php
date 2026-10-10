@extends('layouts.manager')
@section('title', 'Jadwal Maintenance')
@section('content')
<div class="top"><div><h1>Jadwal Maintenance</h1></div>
  <a class="btn primary" href="{{ route('manajemen.jadwal.create') }}">@include('partials.icon', ['n' => 'plus']) Tambah Jadwal</a></div>
@include('manajemen.jadwal._tabs')

<div class="grid g3" style="margin-bottom:18px">
  @if ($s = $cards['lewat'])
  <div class="card sched-card" style="border-top:5px solid var(--r700)">
    <span class="chip c-terlambat" style="align-self:flex-start">Lewat jadwal</span>
    <h4>{{ $s->machine->label }}</h4><div class="muted">{{ $s->task }}{{ $s->interval_label ? ', '.$s->interval_label : '' }}</div>
    <div>Jatuh tempo: <b>{{ $s->due_date->translatedFormat('d M Y') }}</b></div>
    <form method="POST" action="{{ route('manajemen.jadwal.tiket', $s) }}">@csrf<button class="btn primary">Buat tiket</button></form>
  </div>@endif
  @if ($s = $cards['minggu'])
  <div class="card sched-card" style="border-top:5px solid var(--r400)">
    <span class="chip c-minggu" style="align-self:flex-start">Minggu ini</span>
    <h4>{{ $s->machine->label }}</h4><div class="muted">{{ $s->task }}{{ $s->interval_label ? ', '.$s->interval_label : '' }}</div>
    <div>Jatuh tempo: <b>{{ $s->due_date->translatedFormat('d M Y') }}</b></div>
    <form method="POST" action="{{ route('manajemen.jadwal.selesai', $s) }}">@csrf<button class="btn dark">Tandai selesai</button></form>
  </div>@endif
  @if ($s = $cards['terjadwal'])
  <div class="card sched-card" style="border-top:5px solid #d9cfd1">
    <span class="chip c-terjadwal" style="align-self:flex-start">Terjadwal</span>
    <h4>{{ $s->machine->label }}</h4><div class="muted">{{ $s->task }}{{ $s->interval_label ? ', '.$s->interval_label : '' }}</div>
    <div>Jatuh tempo: <b>{{ $s->due_date->translatedFormat('d M Y') }}</b></div>
    <a class="btn" href="{{ route('manajemen.mesin.show', $s->machine) }}">Lihat riwayat</a>
  </div>@endif
</div>

<div class="card" style="margin-bottom:18px;padding:14px 22px">
  <b>Progress:</b> <span class="muted" style="margin-left:10px">25% Foto sebelum · 50% Pengerjaan · 75% Foto sesudah · 100% Selesai</span>
</div>

<div class="card tablewrap">
  <table>
    <thead><tr><th>Mesin</th><th>Jenis</th><th>Jatuh tempo</th><th>Teknisi</th><th>Progress</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($schedules as $s)
      <tr>
        <td><b>{{ $s->machine->label }}</b><div class="tiny">{{ $s->task }}</div></td>
        <td>{{ ucfirst($s->type) }}</td>
        <td>{{ $s->due_date->translatedFormat('d M Y') }}</td>
        <td>{{ $s->technician?->name ?? 'Belum ditugaskan' }}</td>
        <td style="min-width:150px"><div class="row"><div class="progress" style="flex:1"><i style="width:{{ $s->progress }}%"></i></div><b class="small">{{ $s->progress }}%</b></div>
          <div class="tiny">{{ $s->progress == 100 ? 'Bukti lengkap' : ($s->progress ? 'Sedang dikerjakan' : 'Belum mulai') }}</div></td>
        <td>@include('partials.chip', ['v' => $s->display_status])</td>
        <td>
          @if (!$s->technician_id)
            <form class="row" method="POST" action="{{ route('manajemen.jadwal.assign', $s) }}">@csrf
              <select class="select" name="technician_id" style="padding:6px 10px;min-width:120px" onchange="this.form.submit()" required>
                <option value="">Tugaskan…</option>
                @foreach (\App\Models\User::where('role','teknisi')->where('status','aktif')->get() as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
              </select>
            </form>
          @else <a class="link" href="{{ route('manajemen.mesin.show', $s->machine) }}">Detail</a> @endif
        </td>
      </tr>
    @empty <tr><td colspan="7" class="empty">Belum ada jadwal.</td></tr> @endforelse
    </tbody>
  </table>
</div>

<div class="card tablewrap" style="margin-top:18px">
  <h3>Riwayat Servis Terakhir</h3>
  <table><thead><tr><th>Mesin</th><th>Tindakan</th><th>Sparepart</th><th>Teknisi</th><th>Tanggal</th></tr></thead><tbody>
  @forelse ($history as $h)
    <tr><td>{{ $h->machine->code }}</td><td>{{ $h->task }}</td><td>{{ $h->spareparts ?? '-' }}</td><td>{{ $h->technician?->name ?? '-' }}</td><td>{{ $h->completed_at?->translatedFormat('d M Y') }}</td></tr>
  @empty <tr><td colspan="5" class="empty">Belum ada riwayat.</td></tr> @endforelse
  </tbody></table>
</div>
@endsection
