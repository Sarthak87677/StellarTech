<footer id="contact">
  <div class="wrap">
    <div class="foot-grid">
      <div><img class="foot-logo" src="{{ asset('images/logo-full.png') }}" alt="StellarTech Club">
        <p class="foot-blurb">A student-led robotics, AI, space science and heritage-technology organisation.</p></div>
      <div><h4>Explore</h4><ul>
        <li><a href="{{ route('about') }}">About</a></li><li><a href="{{ route('projects') }}">Projects</a></li>
        <li><a href="{{ route('rakhandar') }}">Ultimate Rakhandar</a></li><li><a href="{{ route('home') }}#news">Space news</a></li>
        <li><a href="{{ route('home') }}#leadership">Leadership</a></li></ul></div>
      <div><h4>Members</h4><ul>
        <li><a href="{{ route('apply') }}">Apply to join</a></li><li><a href="{{ route('login') }}">Member login</a></li>
        <li><a href="{{ route('credits') }}">Membership credits</a></li></ul></div>
      <div><h4>Contact</h4><ul>
        <li><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
        <li><a href="{{ route('privacy') }}">Privacy policy</a></li><li><a href="{{ route('terms') }}">Terms &amp; conditions</a></li></ul></div>
    </div>
    <div class="foot-bot">
      <span>© {{ date('Y') }} StellarTech Explorers Club</span>
      <span>stellartechexplorers.com</span>
    </div>
  </div>
</footer>
