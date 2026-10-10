@extends('layouts.manager')
@section('title', $tech->name)
@section('content')
<div class="top"><div><h1>{{ $tech->name }}</h1><p>NIK {{ $tech->nik }} · {{ $tech->department }} · WA {{ $tech->whatsapp ?? '-' }}</p></div><a class="btn" href="{{ route('manajemen.teknisi.index') }}">← Kembali</a></div>
<div class="card tablewrap"><h3>Pekerjaan terbaru</h3>
  <table><thead><tr><th>Tiket</th><th>Mesin</th><th>Progress</th><th>Status</th></tr></thead><tbody>
  @forelse ($tickets as $t)
    <tr><td><a class="link" href="{{ route('manajemen.perbaikan.show', $t) }}">{{ $t->code }}</a></td><td>{{ $t->machine->label }}</td>
    <td><div class="progress"><i style="width:{{ $t->progress }}%"></i></div></td><td>@include('partials.chip', ['v' => $t->display_status])</td></tr>
  @empty <tr><td colspan="4" class="empty">Belum ada pekerjaan.</td></tr> @endforelse
  </tbody></table></div>
@endsection
