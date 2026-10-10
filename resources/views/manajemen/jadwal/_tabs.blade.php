<div class="tabs">
  <a href="{{ route('manajemen.jadwal.index') }}" class="{{ request()->routeIs('manajemen.jadwal.index') ? 'on' : '' }}">Daftar</a>
  <a href="{{ route('manajemen.jadwal.kalender') }}" class="{{ request()->routeIs('manajemen.jadwal.kalender') ? 'on' : '' }}">Kalender Bulanan</a>
</div>
