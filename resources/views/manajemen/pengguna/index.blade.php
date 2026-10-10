@extends('layouts.manager')
@section('title', 'Pengguna')
@section('content')
<div class="top"><div><h1>Pengguna</h1></div>
  <a class="btn primary" href="{{ route('manajemen.pengguna.create') }}">@include('partials.icon', ['n' => 'plus']) Tambah Pengguna</a></div>
<h3 style="margin-bottom:10px;font-size:18px">Menunggu Persetujuan ({{ $pending->count() }})</h3>
<div class="card tablewrap" style="margin-bottom:26px">
  <table><thead><tr><th>Nama</th><th>NIK</th><th>Departemen</th><th>Peran</th><th></th></tr></thead><tbody>
  @forelse ($pending as $u)
    <tr>
      <td><b>{{ $u->name }}</b></td><td>{{ $u->nik }}</td><td>{{ $u->department }}</td>
      <td colspan="2">
        <form class="row" method="POST" action="{{ route('manajemen.pengguna.approve', $u) }}">@csrf
          <select class="select" name="role" style="max-width:190px" required><option value="">Pilih peran</option><option value="teknisi">Teknisi</option><option value="manajemen">Manager</option></select>
          <button class="link">Setujui</button>
          <button class="link danger" formaction="{{ route('manajemen.pengguna.reject', $u) }}" formnovalidate>Tolak</button>
        </form>
      </td>
    </tr>
  @empty <tr><td colspan="5" class="empty">Tidak ada pendaftaran baru.</td></tr> @endforelse
  </tbody></table>
</div>
<h3 style="margin-bottom:10px;font-size:18px">Pengguna Aktif</h3>
<div class="card tablewrap">
  <table><thead><tr><th>Nama</th><th>NIK</th><th>Departemen</th><th>Peran</th><th>Status</th></tr></thead><tbody>
  @foreach ($active as $u)
    <tr><td><div class="person"><div class="avatar" style="width:34px;height:34px;font-size:12px;background:var(--grad-brand);color:#fff">{{ $u->initials }}</div>{{ $u->name }}</div></td><td>{{ $u->nik }}</td><td>{{ $u->department }}</td><td>{{ ($u->role === 'manajemen' || $u->role === \App\Enums\Role::Manajemen) ? 'Manager' : ucfirst($u->role instanceof \App\Enums\Role ? $u->role->value : $u->role) }}</td><td>@include('partials.chip', ['v' => 'aktif'])</td></tr>
  @endforeach
  </tbody></table>
</div>
@endsection
