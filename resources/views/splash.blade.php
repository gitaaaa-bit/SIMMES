<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#1f6080">
<title>{{ config('app.name') }}</title>
<noscript><meta http-equiv="refresh" content="2;url={{ $next }}"></noscript>
<style>
  :root{--bg:#1f6080;--ink:#fff;--sub:#d3e5ee;--or:#ea580c;--cloud:rgba(150,122,102,.6)}
  *,*::before,*::after{box-sizing:border-box}
  html,body{height:100%;margin:0}
  body{background:var(--bg);color:var(--ink);font-family:Inter,system-ui,"Segoe UI",Helvetica,Arial,sans-serif;overflow:hidden}
  .splash{position:relative;min-height:100%;display:flex;align-items:center;justify-content:center;
    padding:env(safe-area-inset-top,0px) 24px env(safe-area-inset-bottom,0px);transition:opacity .5s}
  .splash.out{opacity:0}
  .clouds{position:absolute;left:0;right:0;bottom:0;height:36%;pointer-events:none;overflow:hidden}
  .clouds i{position:absolute;border-radius:50%;animation:float 11s ease-in-out infinite}
  .c1{width:min(62vw,300px);aspect-ratio:1;left:-12%;bottom:-30%;background:var(--cloud)}
  .c2{width:min(40vw,200px);aspect-ratio:1;left:2%;bottom:-22%;background:var(--or);opacity:.8;animation-delay:-3s}
  .c3{width:min(48vw,240px);aspect-ratio:1;left:34%;bottom:-34%;background:var(--cloud);animation-delay:-6s}
  .c4{width:min(50vw,250px);aspect-ratio:1;right:-14%;bottom:-32%;background:var(--or);opacity:.55;animation-delay:-8s}
  .brand{position:relative;text-align:center;margin-top:-8vh}
  .mark{width:64px;height:64px;margin:0 auto 20px;border-radius:16px;background:var(--or);display:grid;place-items:center;
    font-size:34px;font-weight:700;animation:pop .6s ease-out both}
  h1{margin:0;font-size:40px;font-weight:700;letter-spacing:.02em}
  p{margin:8px auto 0;color:var(--sub);font-size:14px;max-width:260px;line-height:1.5}
  .load{width:160px;height:3px;margin:28px auto 0;border-radius:2px;background:rgba(255,255,255,.22);overflow:hidden}
  .load span{display:block;height:100%;width:0%;background:var(--or);border-radius:2px;transition:width .12s linear}
  .skip{position:absolute;top:calc(16px + env(safe-area-inset-top,0px));right:12px;font-size:13px}
  .skip a{color:var(--sub);padding:12px}
  @keyframes float{0%,100%{transform:translate(0,0)}50%{transform:translate(14px,-12px)}}
  @keyframes pop{from{transform:scale(.7);opacity:0}to{transform:none;opacity:1}}
  @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>
<main class="splash" id="splash">
  <div class="clouds" aria-hidden="true"><i class="c1"></i><i class="c2"></i><i class="c3"></i><i class="c4"></i></div>
  <div class="brand">
    <div class="mark" aria-hidden="true">S</div>
    <h1>SIMMES</h1>
    <p>Sistem Informasi Monitoring dan Maintenance Mesin</p>
    <div class="load" role="progressbar" aria-label="Memuat" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span></div>
  </div>
  <div class="skip"><a href="{{ $next }}">Lewati</a></div>
</main>
<script>
  const next = @json($next);
  const bar = document.querySelector('.load span');
  const track = document.querySelector('.load');
  const MIN = 2200;               // durasi minimum loading (ms)
  let loaded = document.readyState === 'complete';
  window.addEventListener('load', () => { loaded = true; });

  const go = () => {
    document.getElementById('splash').classList.add('out');
    setTimeout(() => location.replace(next), 400);
  };

  const run = () => {
    const start = performance.now();
    const tick = (now) => {
      const t = Math.min((now - start) / MIN, 1);
      const eased = 1 - Math.pow(1 - t, 2);          // melambat di akhir
      const p = loaded ? eased : Math.min(eased, 0.9); // tahan di 90% sampai halaman siap
      const pct = Math.round(p * 100);
      bar.style.width = pct + '%';
      track.setAttribute('aria-valuenow', pct);
      if (loaded && t >= 1) { setTimeout(go, 150); return; }
      requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  // Splash hanya tampil sekali per sesi browser.
  if (sessionStorage.getItem('simmes_splash')) {
    location.replace(next);
  } else {
    sessionStorage.setItem('simmes_splash', '1');
    run();
  }
</script>
</body>
</html>