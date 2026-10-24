@extends('layouts.manager')
@section('title', 'Jadwal Maintenance')
@section('content')
<div class="top">
  <div>
    <h1>Jadwal Maintenance</h1>
    <p class="muted small">Antrian jadwal pemeliharaan mesin (terbaru dimasukkan berada di atas).</p>
  </div>
  <a class="btn primary" href="{{ route('manajemen.jadwal.create') }}">
    @include('partials.icon', ['n' => 'plus']) Tambah Jadwal
  </a>
</div>
@include('manajemen.jadwal._tabs')

<div class="grid g3" style="margin-bottom:18px">
  @if ($s = $cards['lewat'])
  <div class="card sched-card" style="border-top:5px solid var(--r700)">
    <span class="chip c-terlambat" style="align-self:flex-start">Lewat jadwal</span>
    <h4>{{ $s->machine->label }}</h4><div class="muted">{{ $s->task }}{{ $s->interval_label ? ', '.$s->interval_label : '' }}</div>
    <div>Jatuh tempo: <b>{{ $s->due_date->translatedFormat('d M Y') }} {{ $s->due_time ? '· Jam '.$s->due_time : '' }}</b></div>
    <form method="POST" action="{{ route('manajemen.jadwal.tiket', $s) }}">@csrf<button class="btn primary">Buat tiket</button></form>
  </div>@endif
  @if ($s = $cards['minggu'])
  <div class="card sched-card" style="border-top:5px solid var(--r400)">
    <span class="chip c-minggu" style="align-self:flex-start">Minggu ini</span>
    <h4>{{ $s->machine->label }}</h4><div class="muted">{{ $s->task }}{{ $s->interval_label ? ', '.$s->interval_label : '' }}</div>
    <div>Jatuh tempo: <b>{{ $s->due_date->translatedFormat('d M Y') }} {{ $s->due_time ? '· Jam '.$s->due_time : '' }}</b></div>
    <form method="POST" action="{{ route('manajemen.jadwal.selesai', $s) }}">@csrf<button class="btn dark">Tandai selesai</button></form>
  </div>@endif
  @if ($s = $cards['terjadwal'])
  <div class="card sched-card" style="border-top:5px solid #d9cfd1">
    <span class="chip c-terjadwal" style="align-self:flex-start">Terjadwal</span>
    <h4>{{ $s->machine->label }}</h4><div class="muted">{{ $s->task }}{{ $s->interval_label ? ', '.$s->interval_label : '' }}</div>
    <div>Jatuh tempo: <b>{{ $s->due_date->translatedFormat('d M Y') }} {{ $s->due_time ? '· Jam '.$s->due_time : '' }}</b></div>
    <a class="btn" href="{{ route('manajemen.mesin.show', [$s->machine, 'from' => 'jadwal']) }}">Lihat riwayat</a>
  </div>@endif
</div>

<div class="card" style="margin-bottom:18px;padding:14px 22px">
  <b>Progress:</b> <span class="muted" style="margin-left:10px">25% Foto sebelum · 50% Pengerjaan · 75% Foto sesudah · 100% Selesai</span>
</div>

<div class="card tablewrap">
  <div class="row between" style="margin-bottom:10px">
    <div class="row" style="gap:8px">
      <h3 style="margin:0">Antrian Jadwal</h3>
      <span class="chip" style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:700">Terbaru di Atas</span>
    </div>
  </div>
  <table>
    <thead><tr><th>Mesin</th><th>Tugas / Jenis</th><th>Waktu Pelaksanaan</th><th>Teknisi</th><th>Progress</th><th>Status</th><th style="text-align:right">Aksi</th></tr></thead>
    <tbody>
    @forelse ($schedules as $s)
      @php
        $tech = $s->technician;
        $kindReminder = $s->display_status === 'terlambat' ? 'Terlambat' : ($s->due_date->isToday() ? 'Hari-H' : 'Pengingat');
        $schedWaLink = ($tech && $tech->whatsapp) ? \App\Services\WhatsAppService::makeScheduleShareLink($s, $kindReminder) : null;

        $latestTicket = $s->ticket ?? $s->machine->tickets->first();
        $sPhotos = [];
        if ($latestTicket) {
            foreach ($latestTicket->media as $m) {
                $sPhotos[] = [
                    'url'   => $m->url,
                    'phase' => $m->phase,
                    'kind'  => $m->kind,
                    'label' => $m->phase === 'after' ? 'Foto Sesudah Perbaikan' : 'Foto Sebelum / Awal'
                ];
            }
        }
        $sLogs = [];
        if ($latestTicket) {
            foreach ($latestTicket->logs as $l) {
                $sLogs[] = ['label' => $l->label, 'note' => $l->note, 'time' => $l->created_at->translatedFormat('d M Y, H:i')];
            }
        }
        $sPayload = [
            'type'           => $s->type,
            'task'           => $s->task,
            'status'         => $s->status,
            'display_status' => $s->display_status,
            'formatted_date' => $s->due_date->translatedFormat('d M Y') . ($s->due_time ? ' Jam '.$s->due_time.' WIB' : ''),
            'machine'        => ['name' => $s->machine->name, 'code' => $s->machine->code, 'location' => $s->machine->location],
            'technician'     => $s->technician ? ['name' => $s->technician->name, 'whatsapp' => $s->technician->whatsapp] : null,
            'wa_url'         => $schedWaLink,
            'ticket'         => $latestTicket ? ['action' => $latestTicket->action, 'spareparts' => $latestTicket->spareparts] : null,
            'photos'         => $sPhotos,
            'logs'           => $sLogs,
            'show_url'       => route('manajemen.mesin.show', [$s->machine, 'from' => 'jadwal']),
        ];
      @endphp
      <tr>
        <td><b>{{ $s->machine->label }}</b><div class="tiny">{{ $s->machine->location }}</div></td>
        <td><b>{{ $s->task }}</b><div class="tiny muted">{{ ucfirst($s->type) }}{{ $s->interval_label ? ' · '.$s->interval_label : '' }}</div></td>
        <td>
          <b>{{ $s->due_date->translatedFormat('d M Y') }}</b>
          <div class="tiny" style="color:var(--brand);font-weight:700">
            @if ($s->due_time) Jam {{ $s->due_time }} WIB @else - @endif
          </div>
        </td>
        <td>
          @if ($tech)
            <b>{{ $tech->name }}</b>
            @if ($schedWaLink)
              <a href="{{ $schedWaLink }}" target="_blank" rel="noopener noreferrer" class="tiny text-ok" style="display:block;font-weight:600;text-decoration:none" title="Kirim Pengingat Jadwal Maintenance via WA">
                WA: {{ $tech->whatsapp }} ↗
              </a>
            @else
              <div class="tiny {{ $tech->whatsapp ? 'text-ok' : 'muted' }}">{{ $tech->whatsapp ? 'WA: '.$tech->whatsapp : 'Belum ada WA' }}</div>
            @endif
          @else
            <form class="row" method="POST" action="{{ route('manajemen.jadwal.assign', $s) }}">@csrf
              <select class="select" name="technician_id" style="padding:5px 8px;font-size:12px;min-width:120px" onchange="this.form.submit()" required>
                <option value="">Tugaskan…</option>
                @foreach (\App\Models\User::where('role','teknisi')->where('status','aktif')->get() as $u)
                  <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </form>
          @endif
        </td>
        <td style="min-width:130px">
          <div class="row"><div class="progress" style="flex:1"><i style="width:{{ $s->progress }}%"></i></div><b class="small">{{ $s->progress }}%</b></div>
          <div class="tiny">{{ $s->progress == 100 ? 'Bukti lengkap' : ($s->progress ? 'Sedang dikerjakan' : 'Belum mulai') }}</div>
        </td>
        <td>@include('partials.chip', ['v' => $s->display_status])</td>
        <td style="text-align:right">
          <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px">
            <button type="button" class="btn-action primary" data-schedule="{{ json_encode($sPayload) }}" onclick="openScheduleDetail(JSON.parse(this.dataset.schedule))" style="padding:4px 9px;font-size:11.5px">
              @include('partials.icon', ['n' => 'eye']) <span>Detail</span>
            </button>
            <a href="{{ route('manajemen.mesin.show', [$s->machine, 'from' => 'jadwal']) }}" class="tiny muted" title="Lihat profil mesin lengkap" style="padding:2px 4px;font-weight:700">↗</a>
            @if ($schedWaLink)
              <a href="{{ $schedWaLink }}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;margin-left:4px;color:#16a34a" title="Kirim Notifikasi Jadwal Maintenance via WA ke {{ $tech->name }}">
                <svg class="i" style="width:16px;height:16px" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
              </a>
            @endif
          </div>
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

@include('partials.job-detail-modal')
@endsection
