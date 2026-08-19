{{--
  Streak flame — top bar, left of the notification bell.

  Expects:
    $days   int   user_streaks.current_streak (0 when there is no record)
    $loggedToday  bool  whether activity has been logged today

  Tier is decided here, server-side, from the real counter. There is no
  placeholder number: a member with no streak record renders tier 0.

  "At risk" is deliberately NOT decided here. It depends on the member's
  local clock being past 18:00, and the server does not know their
  timezone — os.js adds .is-at-risk on the client.
--}}
@php
    $days = (int) ($days ?? 0);
    $tier = match (true) {
        $days >= 21 => 't4',
        $days >= 7  => 't3',
        $days >= 3  => 't2',
        $days >= 1  => 't1',
        default     => 't0',
    };
    $uid   = 'fl' . Str::random(6);
    $label = $days === 1 ? '1 day streak' : $days . ' day streak';
@endphp

<span class="flame flame--{{ $tier }}"
      title="{{ $label }}"
      data-streak="{{ $days }}"
      data-logged-today="{{ ($loggedToday ?? false) ? '1' : '0' }}">
  <svg viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="{{ $uid }}" x1="0" y1="1" x2="0" y2="0">
        <stop class="s1" offset="0"/>
        <stop class="s2" offset="1"/>
      </linearGradient>
    </defs>
    <path class="flame-body" fill="url(#{{ $uid }})"
          d="M13.1 1.9c.4 3.3-.9 5.4-2.5 7.2-1.8 2-4 3.8-4 6.9a6.4 6.4 0 0 0 12.8 0c0-2.4-1.1-4.2-2.5-5.8-.2 1.2-.8 2-1.7 2.4.9-3.6-.5-7.5-2.1-10.7Z"/>
    <path class="flame-core"
          d="M12.8 13c.3 1.7 1.4 2.6 2 3.7a3.1 3.1 0 0 1-6 1.1c0-1.2.6-2.1 1.4-2.9.1.5.4.9.7 1.1-.2-1.4.5-2.5 1.9-3Z"/>
  </svg>
  <span class="flame-embers" aria-hidden="true"><i></i><i></i><i></i></span>
  <span class="sr-only">{{ $label }}</span>
</span>
