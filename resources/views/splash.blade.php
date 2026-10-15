<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#b1002c">
<title>{{ config('app.name', 'SIMMES') }}</title>
<noscript><meta http-equiv="refresh" content="2;url={{ $next }}"></noscript>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
<style>
  :root {
    --red-900: #40000a;
    --red-800: #920022;
    --red-700: #b1002c;
    --red-600: #dc143c;
    --red-500: #ffb3b3;
    --red-400: #ffb3b3;
    --red-200: #e6bdbc;
    --red-100: #ffdad9;
    --red-50:  #fff1f0;
    --slate-900: #1b1c1c;
    --slate-800: #303030;
    --slate-700: #474646;
    --slate-600: #5f5e5e;
    --slate-400: #777575;
    --slate-300: #c8c6c5;
    --slate-200: #eae8e7;
    --slate-100: #efeded;
    --slate-50:  #f5f3f3;
    --grad-brand: #b1002c;
  }

  *, *::before, *::after { box-sizing: border-box; }
  html, body { height: 100%; margin: 0; }
  body {
    background: #fbf9f8;
    color: var(--slate-900);
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    overflow: hidden;
    position: relative;
  }

  .splash {
    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: env(safe-area-inset-top, 20px) 20px env(safe-area-inset-bottom, 20px);
    transition: opacity .45s cubic-bezier(0.4, 0, 0.2, 1), transform .45s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .splash.out {
    opacity: 0;
    transform: scale(1.02);
    pointer-events: none;
  }

  /* SVG Artwork Latar Belakang Merah & Putih */
  .bg-art {
    position: absolute;
    pointer-events: none;
    z-index: 0;
  }
  .wave-top-left {
    top: 0;
    left: 0;
    width: clamp(260px, 45vw, 560px);
    height: clamp(220px, 40vh, 440px);
    animation: driftWaveA 14s ease-in-out infinite;
    filter: drop-shadow(0 12px 28px rgba(220, 38, 38, 0.16));
  }
  .wave-bottom-right {
    bottom: 0;
    right: 0;
    width: clamp(320px, 50vw, 700px);
    height: clamp(250px, 44vh, 520px);
    animation: driftWaveB 16s ease-in-out infinite;
    filter: drop-shadow(0 -10px 28px rgba(220, 38, 38, 0.14));
  }

  @keyframes driftWaveA {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(-6px, 6px); }
  }
  @keyframes driftWaveB {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(6px, -6px); }
  }

  /* Aksen Dekoratif Geometris */
  .deco {
    position: absolute;
    pointer-events: none;
    z-index: 1;
  }
  .deco-tr-circle {
    top: 4%;
    right: 28%;
    width: clamp(50px, 6.5vw, 80px);
    height: clamp(50px, 6.5vw, 80px);
  }
  .deco-tr-dots {
    top: 6%;
    right: 4%;
    width: 90px;
    height: 45px;
  }
  .deco-bl-circle {
    bottom: 16%;
    left: 3%;
    width: clamp(60px, 7vw, 90px);
    height: clamp(60px, 7vw, 90px);
  }
  .deco-bl-dots {
    bottom: 4%;
    left: 4%;
    width: 90px;
    height: 45px;
  }
  .deco-bc-circle {
    bottom: 6%;
    left: 38%;
    width: 64px;
    height: 64px;
    opacity: 0.7;
  }

  /* Kartu Pusat Branding SIMMES */
  .brand-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 430px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: 20px;
    padding: 42px 32px 38px;
    box-shadow: 0 20px 45px -12px rgba(220, 38, 38, 0.14),
                0 8px 24px -4px rgba(15, 23, 42, 0.08),
                0 0 0 1px rgba(226, 232, 240, 0.9);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    animation: cardPop .6s cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  /* Badge Brand SIMMES */
  .brand-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--red-50);
    color: var(--red-600);
    font-size: 12px;
    font-family: 'Poppins', 'Inter', sans-serif;
    font-weight: 600;
    letter-spacing: .12em;
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 20px;
    border: 1px solid var(--red-100);
  }
  .brand-badge svg {
    width: 14px;
    height: 14px;
  }

  /* Logo Mark 'S' */
  .mark-wrapper {
    position: relative;
    margin-bottom: 20px;
  }
  .mark-glow {
    position: absolute;
    inset: -14px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(239, 68, 68, 0.32) 0%, transparent 70%);
    z-index: 0;
    animation: pulseGlow 2.8s ease-in-out infinite;
  }
  .mark-ring {
    position: absolute;
    inset: -6px;
    border-radius: 24px;
    border: 2px dashed rgba(220, 38, 38, 0.28);
    animation: spinRing 24s linear infinite;
    z-index: 1;
  }
  .mark {
    position: relative;
    z-index: 2;
    width: 68px;
    height: 68px;
    border-radius: 18px;
    background: var(--grad-brand);
    display: grid;
    place-items: center;
    color: #ffffff;
    font-size: 34px;
    font-weight: 800;
    letter-spacing: -0.02em;
    box-shadow: 0 14px 28px -6px rgba(220, 38, 38, 0.45);
  }

  .brand-title {
    margin: 0;
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--slate-900);
    line-height: 1.15;
  }

  .brand-sub {
    margin: 8px 0 0;
    color: var(--slate-600);
    font-size: 13.5px;
    line-height: 1.5;
    max-width: 310px;
  }

  /* Progress Bar & Status */
  .load-wrap {
    width: 100%;
    max-width: 290px;
    margin-top: 28px;
  }
  .load {
    width: 100%;
    height: 6px;
    border-radius: 999px;
    background: var(--slate-100);
    border: 1px solid var(--slate-200);
    overflow: hidden;
    position: relative;
  }
  .load span {
    display: block;
    height: 100%;
    width: 0%;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--red-600) 0%, var(--red-500) 55%, #ff4d6a 100%);
    box-shadow: 0 0 10px rgba(220, 38, 38, 0.5);
    transition: width .12s linear;
  }
  .load-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    font-size: 12px;
    color: var(--slate-600);
    font-weight: 600;
  }
  #loadPct {
    color: var(--red-600);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }

  /* Tombol Lewati */
  .skip {
    position: absolute;
    top: calc(18px + env(safe-area-inset-top, 0px));
    right: 20px;
    z-index: 20;
  }
  .skip a {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--slate-600);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--slate-200);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
    transition: all .2s ease;
  }
  .skip a svg {
    width: 14px;
    height: 14px;
    transition: transform .2s ease;
  }
  .skip a:hover {
    color: var(--red-600);
    border-color: var(--red-200);
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.15);
  }
  .skip a:hover svg {
    transform: translateX(2px);
  }

  @keyframes cardPop {
    from { transform: scale(0.92); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
  @keyframes spinRing {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  @keyframes pulseGlow {
    0%, 100% { opacity: 0.35; transform: scale(0.96); }
    50% { opacity: 0.85; transform: scale(1.08); }
  }

  @media (max-width: 480px) {
    .brand-card {
      padding: 34px 22px 30px;
      border-radius: 18px;
    }
    .brand-title {
      font-size: 26px;
    }
    .mark {
      width: 60px;
      height: 60px;
      font-size: 30px;
      border-radius: 16px;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    * { animation: none !important; transition: none !important; }
  }
</style>
</head>
<body>

<!-- Definisi Pattern & Gradient SVG Bersama (Sesuai Layout Auth) -->
<svg width="0" height="0" style="position:absolute;visibility:hidden" aria-hidden="true">
  <defs>
    <!-- Pattern Garis Diagonal Abu-abu -->
    <pattern id="diag-stripes" width="10" height="10" patternTransform="rotate(45 0 0)" patternUnits="userSpaceOnUse">
      <line x1="0" y1="0" x2="0" y2="10" stroke="#cbd5e1" stroke-width="2.5" />
    </pattern>

    <!-- Pattern Titik-Titik Abu-abu -->
    <pattern id="dot-grid" width="16" height="16" patternUnits="userSpaceOnUse">
      <circle cx="3" cy="3" r="1.8" fill="#cbd5e1" />
    </pattern>

    <!-- Pattern Titik-Titik Putih di atas Merah -->
    <pattern id="dot-grid-white" width="14" height="14" patternUnits="userSpaceOnUse">
      <circle cx="3" cy="3" r="1.5" fill="#ffffff" opacity="0.45" />
    </pattern>

    <!-- Gradient Merah Terang (Crimson ke Merah Menyala) -->
    <linearGradient id="redGradMain" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#b91c1c" />
      <stop offset="55%" stop-color="#dc2626" />
      <stop offset="100%" stop-color="#ef4444" />
    </linearGradient>

    <!-- Gradient Merah Gelap untuk Layer Bayangan -->
    <linearGradient id="redGradDark" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#7f1d1d" />
      <stop offset="100%" stop-color="#991b1b" />
    </linearGradient>

    <!-- Gradient Merah Bawah Kanan -->
    <linearGradient id="redGradBR" x1="0%" y1="100%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#dc2626" />
      <stop offset="100%" stop-color="#ef4444" />
    </linearGradient>
  </defs>
</svg>

<main class="splash" id="splash">
  <!-- Gelombang Merah Kiri Atas (Top-Left Waves) -->
  <svg class="bg-art wave-top-left" viewBox="0 0 540 420" preserveAspectRatio="none" aria-hidden="true">
    <path d="M0,0 L440,0 C390,75 350,120 300,140 C235,168 175,160 125,215 C75,270 50,335 0,390 Z" fill="url(#redGradDark)" />
    <path d="M0,0 L370,0 C325,60 285,95 245,112 C185,134 145,152 98,205 C50,258 28,305 0,350 Z" fill="url(#redGradMain)" />
    <rect x="70" y="32" width="84" height="42" fill="url(#dot-grid-white)" />
  </svg>

  <!-- Gelombang Merah & Abu-abu Kanan Bawah (Bottom-Right Waves) -->
  <svg class="bg-art wave-bottom-right" viewBox="0 0 680 500" preserveAspectRatio="none" aria-hidden="true">
    <path d="M680,160 C560,180 490,270 395,305 C295,345 200,355 105,430 C55,460 20,490 0,500 L680,500 Z" fill="#f1f5f9" />
    <path d="M680,250 C580,268 510,355 395,380 C290,405 230,430 175,500 L680,500 Z" fill="url(#redGradDark)" />
    <path d="M680,310 C590,328 530,405 435,420 C350,435 300,460 248,500 L680,500 Z" fill="url(#redGradBR)" />
    <circle cx="585" cy="425" r="32" fill="url(#diag-stripes)" opacity="0.32" />
    <rect x="430" y="445" width="84" height="28" fill="url(#dot-grid-white)" />
  </svg>

  <!-- Elemen Dekoratif Geometris (Konsisten dengan Halaman Login) -->
  <svg class="deco deco-tr-circle" viewBox="0 0 80 80" aria-hidden="true">
    <circle cx="40" cy="40" r="38" fill="url(#diag-stripes)" />
  </svg>
  <svg class="deco deco-tr-dots" viewBox="0 0 96 48" aria-hidden="true">
    <rect width="96" height="48" fill="url(#dot-grid)" />
  </svg>
  <svg class="deco deco-bl-circle" viewBox="0 0 90 90" aria-hidden="true">
    <circle cx="45" cy="45" r="42" fill="url(#diag-stripes)" />
  </svg>
  <svg class="deco deco-bl-dots" viewBox="0 0 96 48" aria-hidden="true">
    <rect width="96" height="48" fill="url(#dot-grid)" />
  </svg>
  <svg class="deco deco-bc-circle" viewBox="0 0 70 70" aria-hidden="true">
    <circle cx="35" cy="35" r="32" fill="url(#diag-stripes)" />
  </svg>

  <!-- Tombol Lewati di Pojok Kanan Atas -->
  <div class="skip">
    <a href="{{ $next }}" aria-label="Lewati ke halaman utama">
      <span>Lewati</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 18 15 12 9 6"></polyline>
      </svg>
    </a>
  </div>

  <!-- Kartu Konten Branding -->
  <div class="brand-card">
    <div class="brand-badge">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
      </svg>
      SIMMES
    </div>

    <div class="mark-wrapper">
      <div class="mark-glow" aria-hidden="true"></div>
      <div class="mark-ring" aria-hidden="true"></div>
      <div class="mark" aria-hidden="true">S</div>
    </div>

    <h1 class="brand-title">SIMMES</h1>
    <p class="brand-sub">Sistem Informasi Monitoring dan Maintenance Mesin</p>

    <div class="load-wrap">
      <div class="load" role="progressbar" aria-label="Memuat aplikasi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <span></span>
      </div>
      <div class="load-meta">
        <span id="loadText">Memuat modul sistem...</span>
        <span id="loadPct">0%</span>
      </div>
    </div>
  </div>
</main>

<script>
  const next = @json($next);
  const bar = document.querySelector('.load span');
  const track = document.querySelector('.load');
  const pctEl = document.getElementById('loadPct');
  const textEl = document.getElementById('loadText');
  const MIN = 2000;               // durasi loading (ms)
  let loaded = document.readyState === 'complete';
  window.addEventListener('load', () => { loaded = true; });

  const go = () => {
    document.getElementById('splash').classList.add('out');
    setTimeout(() => location.replace(next), 400);
  };

  const steps = [
    { at: 0, text: 'Memuat modul sistem...' },
    { at: 30, text: 'Menyiapkan basis data mesin...' },
    { at: 65, text: 'Sinkronisasi status pemeliharaan...' },
    { at: 90, text: 'Selesai, mengalihkan...' }
  ];

  const run = () => {
    const start = performance.now();
    const tick = (now) => {
      const t = Math.min((now - start) / MIN, 1);
      const eased = 1 - Math.pow(1 - t, 2);            // perlambatan alami di akhir
      const p = loaded ? eased : Math.min(eased, 0.9); // tahan di 90% sampai halaman siap
      const pct = Math.round(p * 100);
      bar.style.width = pct + '%';
      track.setAttribute('aria-valuenow', pct);
      if (pctEl) pctEl.textContent = pct + '%';

      if (textEl) {
        for (let i = steps.length - 1; i >= 0; i--) {
          if (pct >= steps[i].at) {
            textEl.textContent = steps[i].text;
            break;
          }
        }
      }

      if (loaded && t >= 1) { setTimeout(go, 180); return; }
      requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  // Jalankan animasi splash screen setiap kali halaman splash diakses
  run();
</script>
</body>
</html>
