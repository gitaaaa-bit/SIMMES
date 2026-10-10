@extends('layouts.manager')
@section('title', 'Laporan')
@section('content')
<div class="top"><div><h1>Laporan</h1><p>Siap cetak untuk HRD / Manager.</p></div>
  <div class="actions noprint">
    <form method="GET"><input class="input" type="month" name="bulan" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()"></form>
    <button class="btn primary" onclick="window.print()">@include('partials.icon', ['n' => 'printer']) Cetak / PDF</button>
  </div></div>

<article class="paper">
  <div class="row between wrap" style="align-items:flex-start">
    <div><h1>Laporan Perbaikan dan Maintenance Mesin</h1><div class="muted">Monitoring Mesin · Untuk HRD / Manager</div></div>
    <div style="text-align:right"><b>Periode: {{ $month->translatedFormat('F Y') }}</b><div class="muted small">Dicetak: {{ now()->translatedFormat('d M Y') }} · oleh {{ auth()->user()->name }}</div></div>
  </div>
  <div class="sumbox">
    <div><b>{{ $summary['total'] }}</b><span>Total pekerjaan</span></div>
    <div><b style="color:var(--ok)">{{ $summary['selesai'] }}</b><span>Selesai</span></div>
    <div><b style="color:var(--warn)">{{ $summary['dikerjakan'] }}</b><span>Dikerjakan</span></div>
    <div><b style="color:var(--r600)">{{ $summary['terlambat'] }}</b><span>Terlambat</span></div>
  </div>

  <h3>Riwayat Pekerjaan</h3>
  <table>
    <thead><tr><th>Mesin</th><th>Sebelum</th><th>Tindakan</th><th>Sesudah</th><th>Status</th></tr></thead>
    <tbody>
    @forelse ($jobs as $j)
      @php $b = $j->mediaOf('before')->first() ?? $j->mediaOf('report')->first(); $a = $j->mediaOf('after')->first(); @endphp
      <tr>
        <td><div class="tiny">{{ $j->created_at->translatedFormat('d M Y') }} · {{ $j->code }}</div><b>{{ $j->machine->label }}</b><div class="muted small">{{ $j->machine->location }}</div>@include('partials.chip', ['v' => $j->type])</td>
        <td><div class="thumb ba" style="width:84px;height:66px">@if ($b)<img src="{{ $b->url }}" alt="">@else Sebelum @endif</div><div class="tiny">{{ \Illuminate\Support\Str::limit($j->complaint, 40) }}</div></td>
        <td><b>{{ $j->action ?? ($j->status === 'selesai' ? 'Selesai' : 'Sedang dikerjakan') }}</b><div class="tiny">Sparepart: {{ $j->spareparts ?? '-' }}</div></td>
        <td>@if ($a)<div class="thumb" style="width:84px;height:66px"><img src="{{ $a->url }}" alt=""></div>@else<span class="tiny">Belum diunggah</span>@endif</td>
        <td>@include('partials.chip', ['v' => $j->display_status])</td>
      </tr>
    @empty <tr><td colspan="5" class="empty">Tidak ada pekerjaan pada periode ini.</td></tr> @endforelse
    </tbody>
  </table>

  <h3>Jadwal Perbaikan Berikutnya</h3>
  <table><thead><tr><th>Mesin</th><th>Jenis perawatan</th><th>Interval</th><th>Jatuh tempo</th></tr></thead><tbody>
  @foreach ($upcoming as $u)
    <tr><td><b>{{ $u->machine->label }}</b></td><td>{{ $u->task }}</td><td>{{ $u->interval_label ?? '-' }}</td><td><b style="color:var(--r600)">{{ $u->due_date->translatedFormat('d M Y') }}</b></td></tr>
  @endforeach
  </tbody></table>

  <div class="sign"><div>Disiapkan oleh (Teknisi)<br><span class="tiny">[Nama] · [Tanggal]</span></div><div>Diketahui (Manager / HRD)<br><span class="tiny">[Nama] · [Tanggal]</span></div></div>
  <p class="tiny" style="margin-top:26px">Dibuat otomatis dari data pekerjaan. Jadwal berikutnya dihitung dari interval maintenance tiap mesin.</p>
</article>
@endsection
