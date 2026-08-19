{{--
  Streak flame — top bar. The flame is the container: the streak number
  sits inside it.

  Expects:
    $days         int   user_streaks.current_streak (0 when no record)
    $longest      int   user_streaks.longest_streak
    $loggedToday  bool  activity already logged today
    $userId       int   scopes the localStorage key so a shared device
                        never replays one member's celebration for another

  Tier is decided here, server-side, from the real counter. There is no
  placeholder: no streak record renders tier 0 with an honest 0.

  "At risk" is NOT decided here. It needs the member's local clock to be
  past 18:00 and the server does not know their timezone — os.js sets it.
--}}
@php
    $days    = (int) ($days ?? 0);
    $longest = (int) ($longest ?? 0);
    $tier = match (true) {
        $days >= 21 => 't4',
        $days >= 7  => 't3',
        $days >= 3  => 't2',
        $days >= 1  => 't1',
        default     => 't0',
    };
    $uid = 'fl' . Str::random(6);

    // Next milestone. Honest about the last one: past 365 there is no
    // further milestone to promise, so we say so rather than inventing one.
    $milestones = [3, 7, 14, 21, 30, 50, 100, 200, 365];
    $next = null;
    foreach ($milestones as $m) {
        if ($m > $days) { $next = $m; break; }
    }

    // The streak page is not built yet. Link to it only once it exists,
    // otherwise render a non-navigating element rather than a dead link.
    $streakUrl = Route::has('dashboard.streaks') ? route('dashboard.streaks') : null;
    $tag       = $streakUrl ? 'a' : 'span';
@endphp

<{{ $tag }} class="flame flame--{{ $tier }}"
   @if ($streakUrl) href="{{ $streakUrl }}" @endif
   data-flame
   data-streak="{{ $days }}"
   data-logged-today="{{ ($loggedToday ?? false) ? '1' : '0' }}"
   data-user="{{ $userId ?? 0 }}"
   aria-describedby="{{ $uid }}-tip">
  <span class="flame-art">
    <svg viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
      <defs>
        <linearGradient id="{{ $uid }}" x1="0" y1="1" x2="0" y2="0">
          <stop class="s1" offset="0"/>
          <stop class="s2" offset="0.45"/>
          <stop class="s3" offset="1"/>
        </linearGradient>
      </defs>
      <path class="flame-body" fill="url(#{{ $uid }})"
            d="M13.1 1.9c.4 3.3-.9 5.4-2.5 7.2-1.8 2-4 3.8-4 6.9a6.4 6.4 0 0 0 12.8 0c0-2.4-1.1-4.2-2.5-5.8-.2 1.2-.8 2-1.7 2.4.9-3.6-.5-7.5-2.1-10.7Z"/>
      <path class="flame-core"
            d="M12.8 13c.3 1.7 1.4 2.6 2 3.7a3.1 3.1 0 0 1-6 1.1c0-1.2.6-2.1 1.4-2.9.1.5.4.9.7 1.1-.2-1.4.5-2.5 1.9-3Z"/>
    </svg>
    <span class="flame-embers" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>
  </span>

  <span class="flame-n" data-flame-n data-len="{{ strlen((string) $days) }}">{{ $days }}</span>

  <span class="os-tip" id="{{ $uid }}-tip" role="tooltip">
    <dl>
      <dt>Current</dt><dd>{{ $days }} {{ Str::plural('day', $days) }}</dd>
      <dt>Longest</dt><dd>{{ $longest }} {{ Str::plural('day', $longest) }}</dd>
    </dl>
    <p class="next">
      @if ($next)
        {{ $next - $days }} {{ Str::plural('day', $next - $days) }} to {{ $next }}
      @else
        Longest milestone reached.
      @endif
    </p>
  </span>
</{{ $tag }}>
