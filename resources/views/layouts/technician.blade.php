@php
  $unclaimedTickets = \App\Models\Ticket::whereNull('technician_id')->where('status', '!=', 'selesai')->count();
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#b1002c">
  <title>@yield('title', 'Teknisi') · SIMMES</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/simmes.css') }}">
  <style>
    .nav-task-badge {
      position: absolute;
      top: 6px;
      right: 22%;
      background: #dc2626;
      color: #fff;
      font-size: 10px;
      font-weight: 800;
      min-width: 17px;
      height: 17px;
      line-height: 17px;
      border-radius: 999px;
      text-align: center;
      padding: 0 4px;
      border: 1.5px solid #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    /* Prompt Izin Notifikasi Ponsel */
    .phone-perm-banner {
      background: #1e293b;
      color: #fff;
      padding: 8px 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      font-size: 11.5px;
    }
    .phone-perm-btn {
      background: #22c55e;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 8px;
      border: 0;
      cursor: pointer;
      white-space: nowrap;
    }
  </style>
</head>
<body class="tech">
<div class="phone">
  <header class="thead">
    @hasSection('back')<a class="back" href="@yield('back')">@include('partials.icon', ['n' => 'back'])</a>@endif
    <h1>@yield('heading', 'SIMMES')</h1>
    @hasSection('sub')<div class="sub">@yield('sub')</div>@endif
  </header>

  {{-- Bar Izin Notifikasi Layar Ponsel jika belum diizinkan --}}
  <div id="phonePermBanner" class="phone-perm-banner" style="display:none">
    <span>🔔 Aktifkan notifikasi perbaikan di layar ponsel Anda</span>
    <button type="button" class="phone-perm-btn" onclick="enablePhoneNotifications()">Aktifkan</button>
  </div>

  <div class="tbody">
    @include('partials.flash')
    @yield('content')
  </div>

  @unless (View::hasSection('nonav'))
  <nav class="bottom">
    <a href="{{ route('teknisi.home') }}" class="{{ request()->routeIs('teknisi.home') ? 'on' : '' }}">@include('partials.icon', ['n' => 'home']) Beranda</a>
    <a href="{{ route('teknisi.tugas.index') }}" class="{{ request()->routeIs('teknisi.tugas.*') ? 'on' : '' }}" style="position:relative">
      @include('partials.icon', ['n' => 'list']) Tugas
      <span id="navTugasBadge" class="nav-task-badge" style="display:{{ $unclaimedTickets > 0 ? 'inline-flex' : 'none' }}">{{ $unclaimedTickets }}</span>
    </a>
    <a href="{{ route('teknisi.laporan.create') }}" class="fab" aria-label="Lapor">@include('partials.icon', ['n' => 'plus'])</a>
    <a href="{{ route('teknisi.laporan.index') }}" class="{{ request()->routeIs('teknisi.laporan.*') || request()->routeIs('teknisi.lapor*') ? 'on' : '' }}">@include('partials.icon', ['n' => 'file']) Laporan</a>
    <a href="{{ route('teknisi.profil') }}" class="{{ request()->routeIs('teknisi.profil*') ? 'on' : '' }}">@include('partials.icon', ['n' => 'user']) Profil</a>
  </nav>
  @endunless
</div>

<script>
// Real-time Phone Screen Notification Engine untuk Teknisi (Siapa Cepat Dia Dapat)
(function() {
  const pollUrl = "{{ route('teknisi.liveAlerts') }}";
  let knownMaxId = 0;
  let isInitialized = false;
  // Simpan referensi notifikasi layar ponsel aktif: map of ticketId -> Notification instance
  const activePhoneNotifications = new Map();

  // Cek dan tampilkan banner izin notifikasi jika belum diaktifkan
  if (window.Notification && Notification.permission === 'default') {
    const banner = document.getElementById('phonePermBanner');
    if (banner) banner.style.display = 'flex';
  }

  window.enablePhoneNotifications = function() {
    if (window.Notification) {
      Notification.requestPermission().then(perm => {
        const banner = document.getElementById('phonePermBanner');
        if (banner) banner.style.display = 'none';
        if (perm === 'granted') {
          playChimeSound();
          try {
            new Notification("✅ Notifikasi Layar Ponsel Aktif!", {
              body: "Anda akan menerima notifikasi setiap kali ada perbaikan mesin baru (siapa cepat dia dapat).",
              icon: "/favicon.ico"
            });
          } catch(e) {}
        }
      });
    }
  };

  function playChimeSound() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(659.25, ctx.currentTime);
      gain.gain.setValueAtTime(0.2, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.35);
    } catch(e) {}
  }

  // Tampilkan Notifikasi di Layar Ponsel (System Notification)
  function triggerPhoneScreenNotification(ticket) {
    if (!window.Notification || Notification.permission !== 'granted') return;

    try {
      const notif = new Notification("⚡ SIMMES: Kerusakan Mesin Baru!", {
        body: `${ticket.machine} butuh perbaikan segera (Siapa cepat dia dapat)! Ketuk untuk ambil tugas.`,
        icon: "/favicon.ico",
        tag: "simmes-ticket-" + ticket.id,
        requireInteraction: false
      });

      notif.onclick = function() {
        window.focus();
        window.location.href = ticket.claim_url;
        notif.close();
      };

      activePhoneNotifications.set(ticket.id, notif);
      playChimeSound();
    } catch(e) {
      console.warn('Native notification tidak dapat dimunculkan:', e);
    }
  }

  // Polling cek status tiket terbuka & otomatis hilangkan notifikasi saat sudah diambil
  async function syncAlerts() {
    try {
      const res = await fetch(pollUrl, { credentials: 'same-origin' });
      if (!res.ok) return;
      const data = await res.json();
      const count = data.count || 0;
      const openIds = data.open_ids || [];
      const currentMaxId = data.max_id || 0;

      // Update badge di navbar Tugas
      const badge = document.getElementById('navTugasBadge');
      if (badge) {
        if (count > 0) {
          badge.style.display = 'inline-flex';
          badge.textContent = count;
        } else {
          badge.style.display = 'none';
        }
      }

      // 1. KETIKA TIKET SUDAH DIAMBIL: Tutup notifikasi di layar ponsel secara otomatis
      activePhoneNotifications.forEach((notifObj, ticketId) => {
        if (!openIds.includes(ticketId) || count === 0) {
          try { notifObj.close(); } catch(e) {}
          activePhoneNotifications.delete(ticketId);
        }
      });

      // 2. KETIKA ADA TIKET BARU MASUK: Munculkan notifikasi di layar ponsel
      if (!isInitialized) {
        knownMaxId = currentMaxId;
        isInitialized = true;
      } else {
        if (currentMaxId > knownMaxId && data.latest) {
          triggerPhoneScreenNotification(data.latest);
          knownMaxId = currentMaxId;
        }
      }
    } catch(e) {}
  }

  // Inisialisasi awal & polling berkala setiap 15 detik
  syncAlerts();
  setInterval(syncAlerts, 15000);
})();
</script>

@stack('scripts')
</body>
</html>
