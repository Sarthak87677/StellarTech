{{--
  Notification bell — top bar.

  Expects:
    $count int  unread notifications. There is no notifications table
                yet, so callers pass 0 and the badge does not render.
                An invented number would be a lie in the UI.

  Still at rest. os.js shakes it once when a notification ARRIVES and
  pops the badge once when the count goes UP. Never on a plain reload.
--}}
@php $count = (int) ($count ?? 0); @endphp

<button type="button" class="bell" data-bell data-count="{{ $count }}"
        aria-label="Notifications{{ $count ? ', ' . $count . ' unread' : '' }}">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
       stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
  </svg>
  <span class="bell-badge" data-bell-badge @if(!$count) hidden @endif>{{ $count }}</span>
</button>
