<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#c8102e">
  <title>@yield('title', 'Teknisi') · SIMMES</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/simmes.css') }}">
</head>
<body class="tech">
<div class="phone">
  <header class="thead">
    @hasSection('back')<a class="back" href="@yield('back')">@include('partials.icon', ['n' => 'back'])</a>@endif
    <h1>@yield('heading', 'SIMMES')</h1>
    @hasSection('sub')<div class="sub">@yield('sub')</div>@endif
  </header>
  <div class="tbody">
    @include('partials.flash')
    @yield('content')
  </div>

  @unless (View::hasSection('nonav'))
  <nav class="bottom">
    <a href="{{ route('teknisi.home') }}" class="{{ request()->routeIs('teknisi.home') ? 'on' : '' }}">@include('partials.icon', ['n' => 'home']) Beranda</a>
    <a href="{{ route('teknisi.tugas.index') }}" class="{{ request()->routeIs('teknisi.tugas.*') ? 'on' : '' }}">@include('partials.icon', ['n' => 'list']) Tugas</a>
    <a href="{{ route('teknisi.laporan.create') }}" class="fab" aria-label="Lapor">@include('partials.icon', ['n' => 'plus'])</a>
    <a href="{{ route('teknisi.laporan.index') }}" class="{{ request()->routeIs('teknisi.laporan.*') || request()->routeIs('teknisi.lapor*') ? 'on' : '' }}">@include('partials.icon', ['n' => 'file']) Laporan</a>
    <a href="{{ route('teknisi.profil') }}" class="{{ request()->routeIs('teknisi.profil*') ? 'on' : '' }}">@include('partials.icon', ['n' => 'user']) Profil</a>
  </nav>
  @endunless
</div>
@stack('scripts')
</body>
</html>
