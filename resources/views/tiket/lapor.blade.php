@extends(auth()->user()->isTeknisi() ? 'layouts.technician' : 'layouts.manager')
@section('title', 'Laporan Perbaikan')
@section('heading', 'Laporan Perbaikan')
@section('content')
@php
  $user = auth()->user();
  $roleStr = $user->isTeknisi() ? 'teknisi' : 'manajemen';
@endphp
@if (!$user->isTeknisi())<div class="top"><div><h1>Laporan Perbaikan</h1></div></div>@endif
<form class="{{ $user->isTeknisi() ? 'tcard' : 'card' }}" style="{{ !$user->isTeknisi() ? 'max-width:620px' : '' }}" method="POST" action="{{ route($roleStr.'.lapor.store') }}" enctype="multipart/form-data">@csrf
  <label class="f" style="margin-top:0">Nama Mesin</label>
  <input class="input" id="machine_name" name="machine_name" list="mlist" value="{{ old('machine_name') }}" placeholder="Contoh: Mesin Press 2, Genset Utama" autocomplete="off" required>
  <datalist id="mlist">@foreach ($machines as $m)<option value="{{ $m->name }}" data-code="{{ $m->code }}" data-loc="{{ $m->location }}">{{ $m->code }} · {{ $m->location }}</option>@endforeach</datalist>
  <span class="tiny" style="color:var(--ink3)">Isi manual nama mesin yang mengalami kendala.</span>
  @error('machine_name') <div class="err">{{ $message }}</div> @enderror

  <div class="row" style="gap:10px;margin-top:12px">
    <div style="flex:1">
      <label class="f" style="margin-top:0">Kode Mesin (Opsional)</label>
      <input class="input" id="machine_code" name="machine_code" value="{{ old('machine_code') }}" placeholder="Contoh: MSN-014">
      <span class="tiny" style="color:var(--ink3)">Otomatis jika kosong</span>
      @error('machine_code') <div class="err">{{ $message }}</div> @enderror
    </div>
    <div style="flex:1">
      <label class="f" style="margin-top:0">Lokasi Mesin</label>
      <input class="input" id="location" name="location" value="{{ old('location') }}" placeholder="Contoh: Produksi A, Lantai 1">
      @error('location') <div class="err">{{ $message }}</div> @enderror
    </div>
  </div>

  <label class="f" style="margin-top:14px">Keluhan</label>
  <textarea class="textarea" name="complaint" rows="4" placeholder="Jelaskan masalah mesin…" required>{{ old('complaint') }}</textarea>

  <label class="f">Tingkat urgensi</label>
  <div class="urg">
    @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $v => $l)
      <label><input type="radio" name="urgency" value="{{ $v }}" @checked(old('urgency', 'sedang') === $v)><span>{{ $l }}</span></label>
    @endforeach
  </div>

  <label class="f">Foto atau video</label>
  <label class="dash" for="lapor-file-input" style="min-height:90px;cursor:pointer">
    <span id="lapor-file-label">📷 Tambah foto / video</span>
    <input id="lapor-file-input" type="file" name="files[]" accept="image/*,video/*" multiple hidden>
  </label>
  <div id="lapor-preview-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px"></div>
  <button class="btn primary block" style="margin-top:22px">Kirim Laporan</button>
</form>
@push('scripts')
<script>
const fileInput = document.getElementById('lapor-file-input');
const fileLabel = document.getElementById('lapor-file-label');
const previewGrid = document.getElementById('lapor-preview-grid');

if (fileInput) {
  fileInput.addEventListener('change', function () {
    previewGrid.innerHTML = '';
    const files = Array.from(this.files || []);
    if (files.length === 0) {
      fileLabel.textContent = '📷 Tambah foto / video';
      return;
    }
    fileLabel.textContent = '✔ ' + files.length + ' file dipilih (klik untuk ganti)';

    files.forEach((file) => {
      const box = document.createElement('div');
      box.style.cssText = 'position:relative;border-radius:12px;overflow:hidden;border:1.5px solid var(--line);aspect-ratio:1;background:#faf6f6;display:flex;align-items:center;justify-content:center;';

      const url = URL.createObjectURL(file);
      if (file.type.startsWith('video/')) {
        const video = document.createElement('video');
        video.src = url;
        video.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        video.controls = false;
        box.appendChild(video);
      } else {
        const img = document.createElement('img');
        img.src = url;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        box.appendChild(img);
      }
      previewGrid.appendChild(box);
    });
  });
}

const mInput = document.getElementById('machine_name');
const mCode = document.getElementById('machine_code');
const mLoc = document.getElementById('location');
const datalist = document.getElementById('mlist');

if (mInput && datalist) {
  mInput.addEventListener('input', function() {
    const val = this.value.trim().toLowerCase();
    const opts = Array.from(datalist.options);
    const matched = opts.find(o => o.value.toLowerCase() === val);
    if (matched) {
      if (!mCode.value && matched.dataset.code) mCode.value = matched.dataset.code;
      if (!mLoc.value && matched.dataset.loc) mLoc.value = matched.dataset.loc;
    }
  });
}
</script>
@endpush
@endsection
