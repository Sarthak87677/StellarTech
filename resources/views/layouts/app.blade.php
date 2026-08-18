<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', 'StellarTech Explorers Club')</title>
<meta name="description" content="@yield('description', 'A student-led engineering organisation working across robotics, artificial intelligence, space science and heritage-preservation technology.')">

<link rel="canonical" href="{{ url()->current() }}">
@stack('meta')

<link rel="icon" href="{{ asset('images/logo-mark.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

{{--
  No build step. Plain CSS and JS served straight from public/assets.
  Bump ASSET_VERSION in .env after editing a file to force browsers
  to fetch the new copy instead of a cached one.
--}}
@php $v = config('app.asset_version', '1'); @endphp
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('assets/css/forms.css') }}?v={{ $v }}">
</head>
<body>

@include('partials.space')
@include('partials.nav')

@yield('content')

@include('partials.footer')

{{-- Three.js from CDN, then our scripts. defer keeps them out of the render path. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>
<script src="{{ asset('assets/js/space.js') }}?v={{ $v }}" defer></script>
<script src="{{ asset('assets/js/app.js') }}?v={{ $v }}" defer></script>

@stack('scripts')
</body>
</html>
