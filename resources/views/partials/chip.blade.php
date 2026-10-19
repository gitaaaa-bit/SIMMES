@php
  $val = strtolower(trim((string)$v));
  $labels = [
    'aktif'       => 'Aktif',
    'nonaktif'    => 'Non-Aktif',
    'perlu servis'=> 'Perlu Servis',
    'selesai'     => 'Selesai',
    'diproses'    => 'Dikerjakan',
    'dikerjakan'  => 'Dikerjakan',
    'ditugaskan'  => 'Menunggu Teknisi',
    'dilaporkan'  => 'Tiket Baru',
    'terlambat'   => 'Lewat Waktu',
    'terjadwal'   => 'Terjadwal',
    'minggu'      => 'Minggu Ini',
    'rutin'       => 'Rutin',
    'insiden'     => 'Insiden',
    'tinggi'      => 'Prioritas Tinggi',
    'sedang'      => 'Prioritas Sedang',
    'rendah'      => 'Prioritas Rendah',
    'pending'     => 'Pending',
  ];
  $displayLabel = $label ?? ($labels[$val] ?? ucfirst($val));
@endphp
<span class="chip c-{{ $val }}"><span class="chip-dot"></span><span>{{ $displayLabel }}</span></span>

