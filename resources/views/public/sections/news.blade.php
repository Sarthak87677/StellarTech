<section class="sec" id="news" style="padding-top:0">
  <div class="wrap">
    <div class="sec-head rv">
      <div class="eyebrow">Space desk</div>
      <h2>What's happening off-planet.</h2>
      <p class="lede" style="margin-top:1.2rem">
        Live headlines from across the spaceflight press. We read this before every space-science
        session — knowing what the field is actually doing beats guessing.
      </p>
    </div>

    @if ($articles->isNotEmpty())
      <div class="news-grid">
        @foreach ($articles as $a)
          <a class="glass news-card" href="{{ $a['url'] }}" target="_blank" rel="noopener noreferrer">
            <div class="news-thumb">
              @if (!empty($a['image']))
                <img src="{{ $a['image'] }}" alt="" loading="lazy" referrerpolicy="no-referrer">
              @endif
            </div>
            <div class="news-body">
              <div class="news-meta">
                <span>{{ $a['source'] }}</span>
                <span class="sep">·</span>
                <time datetime="{{ $a['published_at'] }}">
                  {{ \Carbon\Carbon::parse($a['published_at'])->format('j M Y') }}
                </time>
              </div>
              <h3>{{ $a['title'] }}</h3>
              <p>{{ \Illuminate\Support\Str::limit($a['summary'], 160) }}</p>
            </div>
          </a>
        @endforeach
      </div>

      <div class="news-foot">
        <span>Source: Spaceflight News API</span><span>·</span>
        <span>Aggregated from 40+ outlets including NASA, ESA and SpaceX</span><span>·</span>
        <span>Cached for {{ config('services.news.cache_minutes', 15) }} minutes</span>
      </div>
    @else
      <div class="news-state">
        Headlines are unavailable right now. The news feed comes from an external service —
        everything else on this page works normally.
      </div>
    @endif
  </div>
</section>
