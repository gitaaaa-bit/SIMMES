@extends('layouts.manager')
@section('title', 'Tambah Jadwal')
@section('content')
<div class="top">
  <div>
    <h1>Tambah Jadwal Maintenance</h1>
    <p>Pilih mesin dari database atau input manual mesin baru (otomatis tersimpan ke database).</p>
  </div>
  <a class="btn" href="{{ route('manajemen.jadwal.index') }}">← Kembali</a>
</div>

<form class="card" style="max-width:720px" method="POST" action="{{ route('manajemen.jadwal.store') }}">
  @csrf

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
    <label class="f" style="margin:0">Mesin Target</label>
    <div style="display:flex;gap:6px">
      <button type="button" id="btnModeDb" class="btn" style="padding:5px 12px;font-size:12px;cursor:pointer">
        📁 Pilih dari Database
      </button>
      <button type="button" id="btnModeManual" class="btn" style="padding:5px 12px;font-size:12px;cursor:pointer">
        ✏️ Input Mesin Baru Manual
      </button>
    </div>
  </div>

  <input type="hidden" name="machine_source" id="machineSource" value="{{ $machines->count() > 0 ? old('machine_source', 'database') : 'manual' }}">

  {{-- 1. Pilihan Mesin dari Database --}}
  <div id="secDb" style="{{ $machines->count() > 0 && old('machine_source', 'database') === 'database' ? '' : 'display:none;' }}margin-bottom:14px">
    @if ($machines->count() > 0)
      <select class="select" name="machine_id" id="selectMachine">
        @foreach ($machines as $m)
          <option value="{{ $m->id }}" @selected(old('machine_id') == $m->id)>
            {{ $m->code }} · {{ $m->name }} (Lokasi: {{ $m->location }})
          </option>
        @endforeach
      </select>
      <div class="tiny muted" style="margin-top:4px">Mesin yang sudah ada di database. Jika ingin menambahkan mesin baru, klik <b>✏️ Input Mesin Baru Manual</b> di atas.</div>
    @else
      <div style="padding:12px;background:#f8fafc;border:1.5px dashed var(--line);border-radius:8px;font-size:13px;color:var(--ink2)">
        Belum ada mesin di database. Silakan gunakan form <b>Input Mesin Baru Manual</b> di bawah.
      </div>
    @endif
  </div>

  {{-- 2. Input Mesin Baru Manual --}}
  <div id="secManual" style="{{ $machines->count() == 0 || old('machine_source') === 'manual' ? '' : 'display:none;' }}background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:14px;margin-bottom:14px">
    <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
      <span class="chip" style="background:#16a34a;color:#fff;font-size:11px;font-weight:700">Mesin Baru</span>
      <b style="font-size:13.5px;color:#166534">Input Data Mesin Baru</b>
      <span class="tiny" style="color:#15803d">(Otomatis tersimpan ke database sehingga selanjutnya bisa langsung dipilih)</span>
    </div>
    <div class="grid g2">
      <div>
        <label class="f" style="margin-top:0">Nama Mesin <span style="color:var(--r600)">*</span></label>
        <input class="input" name="new_machine_name" id="newMachineName" value="{{ old('new_machine_name') }}" placeholder="Contoh: Motor Utama, Mesin Bubut 3">
      </div>
      <div>
        <label class="f" style="margin-top:0">Kode Mesin (Opsional)</label>
        <input class="input" name="new_machine_code" value="{{ old('new_machine_code') }}" placeholder="Contoh: MTR-001 (Otomatis jika kosong)">
      </div>
    </div>
    <div style="margin-top:8px">
      <label class="f" style="margin-top:0">Lokasi Mesin</label>
      <input class="input" name="new_machine_location" value="{{ old('new_machine_location') }}" placeholder="Contoh: Produksi B, Lantai 1">
    </div>
  </div>

  <div class="grid g2">
    <div>
      <label class="f">Jenis / Tugas Perawatan</label>
      <input class="input" name="task" value="{{ old('task') }}" placeholder="Contoh: Pelumasan bearing & cek suhu" required>
    </div>
    <div>
      <label class="f">Tipe Pemeliharaan</label>
      <select class="select" name="type">
        <option value="rutin" @selected(old('type') == 'rutin')>Rutin (Preventive)</option>
        <option value="insiden" @selected(old('type') == 'insiden')>Insiden / Korektif</option>
      </select>
    </div>
    <div>
      <label class="f">Interval Siklus (bulan)</label>
      <input class="input" type="number" min="1" name="interval_months" value="{{ old('interval_months', 3) }}">
    </div>
    <div>
      <label class="f">Tanggal Pelaksanaan (Jatuh Tempo)</label>
      <input class="input" type="date" name="due_date" value="{{ old('due_date', now()->format('Y-m-d')) }}" required>
    </div>
  </div>

  <div class="grid g2" style="margin-top:4px">
    <div>
      <label class="f">Waktu / Jam Pelaksanaan</label>
      <input class="input" type="time" name="due_time" value="{{ old('due_time', '09:00') }}" required>
      <div class="tiny muted" style="margin-top:4px">Jam pelaksanaan maintenance yang akan diberitahukan ke teknisi.</div>
    </div>
    <div>
      <label class="f">Tugaskan Teknisi</label>
      <select class="select" name="technician_id">
        <option value="">Belum ditugaskan (Bisa nanti)</option>
        @foreach ($technicians as $u)
          <option value="{{ $u->id }}" @selected(old('technician_id') == $u->id)>
            {{ $u->name }} {{ $u->whatsapp ? '| WA: '.$u->whatsapp : '(Belum ada WA)' }}
          </option>
        @endforeach
      </select>
      <div class="tiny muted" style="margin-top:4px">Pilih teknisi yang bertugas.</div>
    </div>
  </div>

  <div style="margin-top:16px">
    <label style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;cursor:pointer">
      <input type="checkbox" name="send_wa" value="1" checked style="width:18px;height:18px;margin-top:2px;accent-color:#16a34a">
      <div>
        <b style="color:#166534;font-size:13.5px">Kirim Notifikasi WhatsApp Sekarang</b>
        <div class="tiny" style="color:#15803d;margin-top:2px">
          Kirim notifikasi otomatis ke nomor WhatsApp teknisi begitu jadwal disimpan jika teknisi dipilih.
        </div>
      </div>
    </label>
  </div>

  <button class="btn primary block" style="margin-top:22px">
    💾 Simpan Jadwal & Kirim ke Antrian Teratas
  </button>
</form>

@push('scripts')
<script>
(function() {
  const btnDb = document.getElementById('btnModeDb');
  const btnManual = document.getElementById('btnModeManual');
  const secDb = document.getElementById('secDb');
  const secManual = document.getElementById('secManual');
  const source = document.getElementById('machineSource');
  const hasMachines = {{ $machines->count() > 0 ? 'true' : 'false' }};

  function setMode(m) {
    if (m === 'manual' || !hasMachines) {
      source.value = 'manual';
      if (secDb) secDb.style.display = 'none';
      if (secManual) secManual.style.display = 'block';
      if (btnManual) {
        btnManual.style.background = '#dcfce7';
        btnManual.style.borderColor = '#16a34a';
        btnManual.style.color = '#166534';
        btnManual.style.fontWeight = '700';
      }
      if (btnDb) {
        btnDb.style.background = '#fff';
        btnDb.style.borderColor = 'var(--line)';
        btnDb.style.color = 'inherit';
        btnDb.style.fontWeight = 'normal';
      }
    } else {
      source.value = 'database';
      if (secDb) secDb.style.display = 'block';
      if (secManual) secManual.style.display = 'none';
      if (btnDb) {
        btnDb.style.background = '#e2e8f0';
        btnDb.style.borderColor = '#94a3b8';
        btnDb.style.color = '#0f172a';
        btnDb.style.fontWeight = '700';
      }
      if (btnManual) {
        btnManual.style.background = '#fff';
        btnManual.style.borderColor = 'var(--line)';
        btnManual.style.color = 'inherit';
        btnManual.style.fontWeight = 'normal';
      }
    }
  }

  if (btnDb) btnDb.addEventListener('click', () => setMode('database'));
  if (btnManual) btnManual.addEventListener('click', () => setMode('manual'));

  setMode(source.value);
})();
</script>
@endpush
@endsection


