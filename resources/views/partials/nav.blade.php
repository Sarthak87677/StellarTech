<nav class="nav" id="nav">
  <a class="logo" href="{{ route('home') }}" aria-label="StellarTech Club home">
    <img src="{{ asset('images/logo-mark.png') }}" alt="StellarTech Club emblem">
    <span class="logo-txt">STELLARTECH<span>Explorers Club</span></span>
  </a>
  <div class="navlinks">
    <a href="{{ route('about') }}">About</a><a href="{{ route('projects') }}">Projects</a>
    <a href="{{ route('rakhandar') }}">Rakhandar</a><a href="{{ route('home') }}#news">News</a>
    <a href="{{ route('home') }}#leadership">Leadership</a><a href="{{ route('contact') }}">Contact</a>
  </div>
  <div style="display:flex;gap:.6rem;align-items:center">
    <a class="btn btn-ghost" href="{{ route('login') }}">Member login</a>
    <a class="btn btn-primary" href="{{ route('apply') }}">Apply to join</a>
    <button class="burger" aria-label="Open menu">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
    </button>
  </div>
</nav>
