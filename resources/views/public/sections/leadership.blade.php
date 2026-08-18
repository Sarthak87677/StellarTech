<section class="sec" id="leadership" style="padding-top:0">
  <div class="wrap">
    <div class="sec-head mid rv">
      <div class="eyebrow">Who runs this</div>
      <h2>Founder &amp; Organising Committee</h2>
      <p class="lede" style="margin-top:1.2rem">
        The organising committee sets project direction, reviews member work and approves what goes
        public. Photographs and names are added by the founder from the dashboard.
      </p>
    </div>

    {{-- Founder --}}
    <div class="glass founder">
      <div class="portrait">
        <span class="corner c1"></span><span class="corner c2"></span>
        <span class="corner c3"></span><span class="corner c4"></span>
        @if ($founder && $founder->avatar_path)
          <img src="{{ Storage::disk('public_media')->url($founder->avatar_path) }}"
               alt="{{ $founder->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
        @else
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
            <circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c0-4.1 3.4-6.5 7.5-6.5s7.5 2.4 7.5 6.5"/></svg>
          <small>Founder portrait<br>to be uploaded</small>
        @endif
      </div>
      <div>
        <span class="founder-role">Founder &amp; Club Director</span>
        <h3>{{ $founder?->name ?: 'Name to be added' }}</h3>
        @if (setting('founder_bio'))
          <p>{{ setting('founder_bio') }}</p>
        @else
          <p>A short biography goes here — background, disciplines led, and current work.
             This block is editable from the founder dashboard, so it stays current without touching code.</p>
        @endif
        <p style="font-size:.85rem;color:var(--ink-mute);margin:0">
          Contact: <a href="mailto:{{ setting('contact_email') }}" style="color:var(--blue)">{{ setting('contact_email') }}</a>
        </p>
      </div>
    </div>

    {{-- Committee --}}
    <div class="oc-grid" style="margin-top:1rem">
      @forelse ($committee as $member)
        <div class="glass oc-card">
          <div class="portrait">
            <span class="corner c1"></span><span class="corner c2"></span>
            <span class="corner c3"></span><span class="corner c4"></span>
            @if ($member->avatar_path)
              <img src="{{ Storage::disk('public_media')->url($member->avatar_path) }}"
                   alt="{{ $member->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
            @else
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                <circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c0-4.1 3.4-6.5 7.5-6.5s7.5 2.4 7.5 6.5"/></svg>
              <small>Photo pending</small>
            @endif
          </div>
          <div class="oc-name">{{ $member->name }}</div>
          <div class="oc-role">{{ $member->role->name }}</div>
        </div>
      @empty
        @foreach (['Selected OC — Robotics', 'Selected OC — AI & Vision', 'OC — Documentation'] as $slot)
          <div class="glass oc-card">
            <div class="portrait">
              <span class="corner c1"></span><span class="corner c2"></span>
              <span class="corner c3"></span><span class="corner c4"></span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                <circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c0-4.1 3.4-6.5 7.5-6.5s7.5 2.4 7.5 6.5"/></svg>
              <small>Photo pending</small>
            </div>
            <div class="oc-name">Name to be added</div>
            <div class="oc-role">{{ $slot }}</div>
          </div>
        @endforeach
      @endforelse

      @if (setting('applications_open', '1') === '1')
        <a class="glass oc-card" href="{{ route('apply') }}" style="display:block">
          <div class="portrait" style="border-style:dashed">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
              <path d="M12 5v14M5 12h14"/></svg>
            <small>Open position</small>
          </div>
          <div class="oc-name">Applications open</div>
          <div class="oc-role">Apply to join the committee</div>
        </a>
      @endif
    </div>
  </div>
</section>
