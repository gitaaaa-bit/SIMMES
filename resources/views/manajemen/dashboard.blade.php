@extends('layouts.manager')
@section('title', 'Dashboard')
@section('content')
<div class="top" style="align-items:center;margin-bottom:26px">
  <div>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
      <span class="live-status-pill">
        <span class="pulse-dot"></span>
        Sistem Monitoring Aktif
      </span>
      <span class="tiny muted">{{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
    <h1 style="margin:0 0 4px">Dashboard</h1>
    <p style="margin:0">Halo <b>{{ auth()->user()->name }}</b> , Pantau kesiapan mesin, antrian pemeliharaan rutin, dan tiket perbaikan terkini.</p>
  </div>
  <div class="actions">
    <a class="btn primary" href="{{ route('manajemen.jadwal.create') }}">
      @include('partials.icon', ['n' => 'plus']) Buat Jadwal Baru
    </a>
    <a class="btn" href="{{ route('manajemen.lapor') }}" style="background:#fff">
      @include('partials.icon', ['n' => 'wrench']) Lapor Kerusakan
    </a>
    <a class="btn" href="{{ route('manajemen.laporan.index') }}" style="background:#fff">
      @include('partials.icon', ['n' => 'file']) Rekap Laporan
    </a>
  </div>
</div>

{{-- KPI STAT CARDS INTERAKTIF --}}
<div class="grid g4" style="margin-bottom:24px">
  {{-- Card 1: Mesin Aktif --}}
  <a href="{{ route('manajemen.mesin.index') }}" class="stat-card" title="Klik untuk mengelola seluruh data mesin">
    <div class="stat-card-head">
      <span class="stat-card-title">Mesin Aktif Beroperasi</span>
      <div class="stat-card-ic" style="background:#ecfdf5;color:#047857">
        @include('partials.icon', ['n' => 'cog'])
      </div>
    </div>
    <div class="stat-card-val">{{ $stats['aktif'] }}</div>
    <div class="stat-card-status" style="color:#047857">
      <span class="chip-dot" style="background:#10b981"></span> Siap Berproduksi
    </div>
    <div class="stat-card-foot">
      <span>Kelola data mesin</span>
      @include('partials.icon', ['n' => 'arrow-right'])
    </div>
  </a>

  {{-- Card 2: Sedang Diperbaiki --}}
  <a href="{{ route('manajemen.perbaikan.index') }}" class="stat-card" title="Klik untuk melihat tiket perbaikan yang sedang dikerjakan">
    <div class="stat-card-head">
      <span class="stat-card-title">Sedang Diperbaiki</span>
      <div class="stat-card-ic" style="background:#eff6ff;color:#1d4ed8">
        @include('partials.icon', ['n' => 'wrench'])
      </div>
    </div>
    <div class="stat-card-val">{{ $stats['diperbaiki'] }}</div>
    <div class="stat-card-status" style="color:#1d4ed8">
      <span class="chip-dot" style="background:#3b82f6"></span> Penanganan Teknisi
    </div>
    <div class="stat-card-foot">
      <span>Pantau perbaikan teknisi</span>
      @include('partials.icon', ['n' => 'arrow-right'])
    </div>
  </a>

  {{-- Card 3: Menunggu Teknisi --}}
  <a href="{{ route('manajemen.perbaikan.index') }}" class="stat-card" title="Klik untuk menugaskan teknisi pada tiket masuk">
    <div class="stat-card-head">
      <span class="stat-card-title">Menunggu Respon Teknisi</span>
      <div class="stat-card-ic" style="background:#fffbeb;color:#b45309">
        @include('partials.icon', ['n' => 'clock'])
      </div>
    </div>
    <div class="stat-card-val">{{ $stats['menunggu'] }}</div>
    <div class="stat-card-status" style="color:#b45309">
      <span class="chip-dot" style="background:#f59e0b"></span> Perlu Penugasan
    </div>
    <div class="stat-card-foot">
      <span>Tugaskan teknisi sekarang</span>
      @include('partials.icon', ['n' => 'arrow-right'])
    </div>
  </a>

  {{-- Card 4: Melewati Batas Waktu --}}
  <a href="{{ route('manajemen.perbaikan.index') }}" class="stat-card" title="Klik untuk mengevaluasi tiket yang melewati batas waktu">
    <div class="stat-card-head">
      <span class="stat-card-title">Melewati Batas Waktu</span>
      <div class="stat-card-ic" style="background:#fff1f2;color:#be123c">
        @include('partials.icon', ['n' => 'alert'])
      </div>
    </div>
    <div class="stat-card-val" style="@if($stats['terlambat'] > 0) color:#be123c @endif">{{ $stats['terlambat'] }}</div>
    <div class="stat-card-status" style="@if($stats['terlambat'] > 0) color:#be123c @else color:#047857 @endif">
      @if ($stats['terlambat'] > 0)
        <span class="chip-dot" style="background:#e11d48"></span> Butuh Evaluasi Segera
      @else
        <span class="chip-dot" style="background:#10b981"></span> Semua Sesuai Jadwal
      @endif
    </div>
    <div class="stat-card-foot">
      <span>Tinjau kendala tiket</span>
      @include('partials.icon', ['n' => 'arrow-right'])
    </div>
  </a>
</div>

{{-- SECTION 1: ANTRIAN JADWAL MAINTENANCE --}}
<div class="card tablewrap" style="margin-bottom:24px;padding:22px">
  <div class="row between dashboard-section-head" style="margin-bottom:16px;align-items:flex-start">
    <div class="dashboard-section-title">
      <div class="dashboard-title-line" style="display:flex;align-items:center;gap:10px">
        <h2 style="margin:0;font-size:18px">Antrian Jadwal Maintenance</h2>
        <span class="chip" style="background:#fffbeb;color:#b45309;border-color:#fde68a;font-size:11.5px">
          <span class="chip-dot" style="background:#f59e0b"></span> Urutan Terdekat & Terkini
        </span>
      </div>
      <div class="small muted" style="margin-top:4px">
        Daftar pemeliharaan preventif yang harus segera dikerjakan oleh teknisi sesuai tenggat waktu.
      </div>
    </div>
    <div class="row dashboard-section-actions" style="gap:8px">
      <a class="btn-action primary" href="{{ route('manajemen.jadwal.create') }}">
        @include('partials.icon', ['n' => 'plus'])
        <span>Tambah Jadwal</span>
      </a>
      <a class="btn-action" href="{{ route('manajemen.jadwal.index') }}">
        <span>Semua Jadwal & Kalender →</span>
      </a>
    </div>
  </div>

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="background:#fafaf9">
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Mesin & Lokasi</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Tugas & Jenis</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Waktu Pelaksanaan</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Teknisi Bertugas</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Status</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line);text-align:right">Aksi</th>
      </tr>
    </thead>
    <tbody>
    @forelse ($upcoming as $s)
      @php
        $tech = $s->technician;
      @endphp
      <tr>
        <td style="padding:14px">
          <div style="font-weight:700;font-size:14px;color:var(--ink)">{{ $s->machine->label }}</div>
          <div class="row" style="gap:6px;margin-top:4px">
            <span class="badge-code">{{ $s->machine->code }}</span>
            <span class="tiny muted">{{ $s->machine->location }}</span>
          </div>
        </td>
        <td style="padding:14px">
          <div style="font-weight:600;color:var(--ink)">{{ $s->task }}</div>
          <div class="tiny muted" style="margin-top:3px">
            <span style="text-transform:capitalize">{{ $s->type }}</span>{{ $s->interval_label ? ' · Siklus '.$s->interval_label : '' }}
          </div>
        </td>
        <td style="padding:14px">
          <div style="font-weight:700;color:var(--ink)">{{ $s->due_date->translatedFormat('d M Y') }}</div>
          <div style="margin-top:3px">
            @if ($s->due_time)
              <span class="badge-time">
                @include('partials.icon', ['n' => 'clock']) Jam {{ $s->due_time }} WIB
              </span>
            @else
              <span class="tiny muted">Fleksibel</span>
            @endif
          </div>
        </td>
        <td style="padding:14px">
          @if ($tech)
            <div style="display:flex;align-items:center;gap:8px">
              <div class="avatar" style="width:30px;height:30px;font-size:11px;background:#f3f4f6;color:var(--ink);border:1px solid #e5e7eb">
                {{ $tech->initials }}
              </div>
              <div>
                <div style="font-weight:700;font-size:13px">{{ $tech->name }}</div>
                @if ($tech->whatsapp)
                  <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tech->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="badge-wa" style="margin-top:2px" title="Chat WhatsApp teknisi">
                    @include('partials.icon', ['n' => 'whatsapp']) {{ $tech->whatsapp }}
                  </a>
                @else
                  <span class="badge-wa-empty">No WA belum diisi</span>
                @endif
              </div>
            </div>
          @else
            <span class="chip c-ditugaskan" style="font-size:11px;padding:2px 8px">
              <span class="chip-dot"></span> Belum Ditugaskan
            </span>
          @endif
        </td>
        <td style="padding:14px">
          @include('partials.chip', ['v' => $s->display_status])
        </td>
        <td style="padding:14px;text-align:right">
          <a class="btn-action" href="{{ route('manajemen.mesin.show', [$s->machine, 'from' => 'dashboard']) }}" title="Lihat riwayat dan detail mesin">
            @include('partials.icon', ['n' => 'eye'])
            <span>Detail Mesin</span>
          </a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="empty" style="padding:36px 20px">
          <div style="font-weight:600;color:var(--ink2)">Belum ada antrian jadwal maintenance aktif</div>
          <div class="small muted" style="margin-top:4px">Seluruh jadwal telah diselesaikan atau belum ada jadwal baru yang ditambahkan.</div>
        </td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

{{-- SECTION 2: TIKET KERUSAKAN TERBARU --}}
<div class="card tablewrap" style="margin-bottom:24px;padding:22px">
  <div class="row between dashboard-section-head" style="margin-bottom:16px;align-items:flex-start">
    <div class="dashboard-section-title">
      <div class="dashboard-title-line" style="display:flex;align-items:center;gap:10px">
        <h2 style="margin:0;font-size:18px">Tiket Kerusakan & Perbaikan Terbaru</h2>
        <span class="chip" style="background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;font-size:11.5px">
          <span class="chip-dot" style="background:#3b82f6"></span> Laporan Terkini
        </span>
      </div>
      <div class="small muted" style="margin-top:4px">
        Laporan kerusakan mesin yang baru masuk dan membutuhkan tindak lanjut teknisi.
      </div>
    </div>
    <div class="dashboard-section-actions">
      <a class="btn-action" href="{{ route('manajemen.perbaikan.index') }}">
        <span>Lihat Semua Tiket Perbaikan →</span>
      </a>
    </div>
  </div>

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="background:#fafaf9">
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Tiket</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Mesin & Lokasi</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Kendala / Keluhan</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Urgensi</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line)">Status Pengerjaan</th>
        <th style="padding:12px 14px;border-bottom:1px solid var(--line);text-align:right">Aksi</th>
      </tr>
    </thead>
    <tbody>
    @forelse ($tickets as $t)
      <tr>
        <td style="padding:14px">
          <div style="font-weight:700;font-size:13.5px;color:var(--ink)">{{ $t->code }}</div>
          <div class="tiny muted" style="margin-top:2px">{{ $t->created_at->diffForHumans() }}</div>
        </td>
        <td style="padding:14px">
          <div style="font-weight:700;color:var(--ink)">{{ $t->machine->label }}</div>
          <div class="row" style="gap:6px;margin-top:4px">
            <span class="badge-code">{{ $t->machine->code }}</span>
            <span class="tiny muted">{{ $t->machine->location }}</span>
          </div>
        </td>
        <td style="padding:14px">
          <div style="font-weight:500;color:var(--ink);max-width:260px;line-height:1.4" title="{{ $t->complaint }}">
            {{ Str::limit($t->complaint, 55) }}
          </div>
          @if ($t->technician)
            <div class="tiny muted" style="margin-top:3px">Teknisi: <b>{{ $t->technician->name }}</b></div>
          @endif
        </td>
        <td style="padding:14px">
          @include('partials.chip', ['v' => $t->urgency])
        </td>
        <td style="padding:14px">
          @include('partials.chip', ['v' => $t->display_status])
        </td>
        <td style="padding:14px;text-align:right">
          <a class="btn-action" href="{{ route('manajemen.perbaikan.show', [$t, 'from' => 'dashboard']) }}" title="Buka rincian tiket perbaikan">
            @include('partials.icon', ['n' => 'arrow-right'])
            <span>Buka Tiket</span>
          </a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="empty" style="padding:36px 20px">
          <div style="font-weight:600;color:var(--ink2)">Belum ada tiket perbaikan</div>
          <div class="small muted" style="margin-top:4px">Tidak ada laporan kerusakan aktif yang tercatat saat ini.</div>
        </td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

{{-- SECTION 3: BOTTOM WIDGETS (GRAFIK & PENGINGAT WA) --}}
<div class="grid g2" style="margin-top:24px">
  {{-- Grafik Tren --}}
  <div class="card" style="padding:22px">
    <div class="row between" style="margin-bottom:14px;align-items:flex-start">
      <div>
        <h3 style="margin:0;font-size:16px">Tren Tiket Perbaikan (6 Bulan Terakhir)</h3>
        <div class="small muted" style="margin-top:4px">Volume laporan kerusakan bulanan dari seluruh area pabrik.</div>
      </div>
      <span class="chip" style="background:#f1f5f9;color:#334155;border-color:#e2e8f0;font-size:11.5px">
        Total: {{ $trend->sum('count') }} Tiket
      </span>
    </div>
    @php $max = max(1, $trend->max('count')); @endphp
    <div class="bars" style="margin-top:20px;height:160px">
      @foreach ($trend as $b)
        <div>
          <i style="height:{{ max(8, $b['count'] / $max * 100) }}%;transition:all .3s" title="{{ $b['label'] }}: {{ $b['count'] }} tiket">
            <b>{{ $b['count'] }}</b>
          </i>
          <span style="font-weight:600;margin-top:4px">{{ $b['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Status Otomasi WhatsApp --}}
  <div class="card" style="padding:22px;display:flex;flex-direction:column;justify-content:space-between">
    <div>
      <div class="row between" style="margin-bottom:14px;align-items:flex-start">
        <div>
          <h3 style="margin:0;font-size:16px">Integrasi Notifikasi WhatsApp Gateway</h3>
          <div class="small muted" style="margin-top:4px">Sistem pengingat berkala otomatis ke nomor teknisi.</div>
        </div>
        <a class="link small" href="{{ route('manajemen.jadwal.index') }}" style="font-weight:700">Kelola Jadwal →</a>
      </div>

      <div style="background:#f0fdf4;padding:12px 14px;border-radius:10px;border:1px solid #bbf7d0;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div style="display:flex;align-items:center;gap:8px">
          <span class="pulse-dot"></span>
          <span style="font-weight:700;color:#166534;font-size:13px">Gateway Notifikasi Aktif & Terhubung</span>
        </div>
        <span class="badge-wa">Online 24/7</span>
      </div>

      <div style="display:grid;gap:10px;font-size:12.5px;color:var(--ink2)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#fafaf9;border-radius:8px;border:1px solid var(--line)">
          <div style="display:flex;align-items:center;gap:8px">
            <span style="color:#10b981">✓</span>
            <span>Pengingat Sebelum Jatuh Tempo (H-7 dan H-1)</span>
          </div>
          <span class="chip" style="font-size:10.5px;padding:2px 8px;background:#ecfdf5;color:#047857;border-color:#a7f3d0">Otomatis</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#fafaf9;border-radius:8px;border:1px solid var(--line)">
          <div style="display:flex;align-items:center;gap:8px">
            <span style="color:#10b981">✓</span>
            <span>Pengingat Saat Hari-H Pelaksanaan Maintenance</span>
          </div>
          <span class="chip" style="font-size:10.5px;padding:2px 8px;background:#ecfdf5;color:#047857;border-color:#a7f3d0">Sesuai Jam</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#fafaf9;border-radius:8px;border:1px solid var(--line)">
          <div style="display:flex;align-items:center;gap:8px">
            <span style="color:#ef4444">!</span>
            <span>Peringatan Keterlambatan Pengerjaan (Eskalasi)</span>
          </div>
          <span class="chip" style="font-size:10.5px;padding:2px 8px;background:#fff1f2;color:#be123c;border-color:#fecdd3">Prioritas</span>
        </div>
      </div>
    </div>

    <div style="margin-top:16px;padding-top:12px;border-top:1px solid var(--line);display:flex;justify-content:space-between;align-items:center">
      <span class="tiny muted">Notifikasi dikirim langsung ke WhatsApp teknisi terdaftar.</span>
      <form method="POST" action="{{ route('manajemen.jadwal.runReminders') }}" style="margin:0">
        @csrf
        <button type="submit" class="btn-action" style="font-size:12px;padding:5px 10px" title="Jalankan pengecekan pengingat hari ini">
          @include('partials.icon', ['n' => 'clock'])
          <span>Cek Pengingat Sekarang</span>
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
