<section class="sec" id="media" style="padding-top:0">
  <div class="wrap">
    <div class="sec-head rv">
      <div class="eyebrow">From the workshop</div>
      <h2>Field and build documentation.</h2>
      <p class="lede" style="margin-top:1.2rem">
        Photographs and video from our own sessions. Nothing appears here until a member uploads it
        and the founder approves it for public release.
      </p>
    </div>

    <div class="rail">
      @forelse ($media as $item)
        <figure class="slot" style="padding:0;border-style:solid;overflow:hidden;position:relative">
          @if ($item->type === 'image')
            <img src="{{ Storage::disk('public_media')->url($item->public_path) }}"
                 alt="{{ $item->caption }}" loading="lazy"
                 style="width:100%;height:100%;object-fit:cover">
          @elseif ($item->type === 'embed')
            <iframe src="{{ $item->embed_url }}" title="{{ $item->caption }}"
                    loading="lazy" allowfullscreen
                    style="width:100%;height:100%;border:0"></iframe>
          @endif
          <figcaption style="position:absolute;inset:auto 0 0 0;padding:.9rem 1rem;
                background:linear-gradient(transparent,rgba(2,4,10,.92));text-align:left">
            <b style="display:block">{{ $item->caption }}</b>
            <small>{{ optional($item->captured_at)->format('j M Y') }}
              @if ($item->team_names) · {{ $item->team_names }} @endif
            </small>
          </figcaption>
        </figure>
      @empty
        @foreach (['No approved media yet' => 'Awaiting first upload',
                   'Build session footage'  => 'Slot reserved',
                   'Field documentation'    => 'Slot reserved'] as $label => $note)
          <div class="slot">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
              <rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/>
              <path d="M3 17l5-4 4 3 3-2 6 5"/></svg>
            <b>{{ $label }}</b><small>{{ $note }}</small>
          </div>
        @endforeach
      @endforelse
    </div>

    @if ($media->isEmpty())
      <p style="font-family:var(--mono);font-size:.63rem;letter-spacing:.14em;text-transform:uppercase;
                color:var(--ink-mute);margin-top:.4rem">
        Placeholder slots — real captions, dates, tags and team credits attach on upload
      </p>
    @endif
  </div>
</section>
