@extends('layouts.app')
@section('title', 'Motion preview — StellarTech OS')
@push('meta')
<meta name="robots" content="noindex, nofollow">
{{-- os.css is not in the public layout: this page is the first thing to use it. --}}
<link rel="stylesheet" href="{{ asset('assets/css/os.css') }}?v={{ config('app.asset_version', '1') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/os.js') }}?v={{ config('app.asset_version', '1') }}" defer></script>
@endpush

@section('content')
<div class="dash">
  <div class="wrap">

    <div class="dash-head">
      <div>
        <h1>Motion preview</h1>
        <span class="dash-role">Internal · founder only</span>
      </div>
      <a class="btn btn-ghost" href="{{ route('dashboard.index') }}">Back to dashboard</a>
    </div>

    <div class="glass panel" style="margin-bottom:1rem">
      <p style="font-size:.9rem;color:var(--ink-soft);margin:0">
        Both effects are one-shot and fire on a state change. At rest they do not move.
        Use the buttons to trigger the state changes you would otherwise have to wait
        for. Nothing here writes to the database.
      </p>
    </div>

    {{-- ---------------- streak flame ---------------- --}}
    <div class="glass panel" style="margin-bottom:1rem">
      <h2>Streak flame</h2>

      <div class="mo-stage">
        @include('partials.os-flame', [
            'days' => 6, 'longest' => 19, 'loggedToday' => true, 'userId' => 'demo',
        ])
      </div>

      <div class="mo-controls">
        <button class="btn btn-primary" data-demo="streak-up">Streak increased</button>
        <button class="btn" data-demo="streak-tier">Cross a tier (6 → 7)</button>
        <button class="btn" data-demo="streak-big">Into tier 4 (→ 21)</button>
        <button class="btn btn-ghost" data-demo="streak-risk">Toggle at risk</button>
        <button class="btn btn-ghost" data-demo="streak-reset">Reset to 6</button>
      </div>

      <p class="mo-note">
        Hover the flame for the tooltip. In production the celebration fires on page
        load only when the streak is higher than the value stored at the last visit,
        so a plain reload animates nothing.
      </p>
    </div>

    {{-- ---------------- tier reference ---------------- --}}
    <div class="glass panel" style="margin-bottom:1rem">
      <h2>Tiers at rest</h2>
      <div class="mo-tiers">
        @foreach ([['d'=>0,'l'=>'0 · none'], ['d'=>2,'l'=>'1 · 1–2'], ['d'=>5,'l'=>'2 · 3–6'],
                   ['d'=>12,'l'=>'3 · 7–20'], ['d'=>34,'l'=>'4 · 21+']] as $t)
          <div class="mo-tier">
            @include('partials.os-flame', [
                'days' => $t['d'], 'longest' => 34, 'loggedToday' => true,
                'userId' => 'demo-t' . $t['d'],
            ])
            <span class="mo-lab">{{ $t['l'] }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- ---------------- bell ---------------- --}}
    <div class="glass panel">
      <h2>Notification bell</h2>

      <div class="mo-stage">
        @include('partials.os-bell', ['count' => 0])
      </div>

      <div class="mo-controls">
        <button class="btn btn-primary" data-demo="notify">Notification arrived</button>
        <button class="btn" data-demo="notify-3">Three arrive at once</button>
        <button class="btn btn-ghost" data-demo="notify-clear">Mark all read</button>
      </div>

      <p class="mo-note">
        No badge at zero. The count is real state, never a placeholder number — the
        notifications table does not exist yet, so production passes 0 and the bell
        sits still.
      </p>
    </div>

  </div>
</div>

<style>
  .mo-stage{display:flex;align-items:center;justify-content:center;gap:2rem;
    padding:2.2rem 1rem;margin-bottom:1.1rem;border-radius:12px;
    background:rgba(6,10,22,.55);border:1px solid var(--line-soft)}
  .mo-controls{display:flex;gap:.5rem;flex-wrap:wrap}
  .mo-controls .btn{padding:.5rem .9rem;font-size:.78rem}
  .mo-note{font-size:.8rem;color:var(--ink-mute);margin:1rem 0 0;line-height:1.6}
  .mo-tiers{display:flex;gap:.9rem;flex-wrap:wrap}
  .mo-tier{flex:1 1 108px;display:flex;flex-direction:column;align-items:center;gap:.7rem;
    padding:1.1rem .5rem;border-radius:10px;background:rgba(6,10,22,.55);
    border:1px solid var(--line-soft)}
  .mo-lab{font-family:var(--mono);font-size:.57rem;letter-spacing:.12em;
    text-transform:uppercase;color:var(--ink-mute)}
</style>

<script>
/* Demo wiring only. Lives on this page, never in os.js. */
document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-demo]');
  if (!btn || !window.OsMotion) return;
  const flame = document.querySelector('.mo-stage [data-flame]');
  const current = flame ? (parseInt(flame.dataset.streak, 10) || 0) : 0;

  // Scope every trigger to the staged flame — the tier row below is a
  // static reference and must not animate.
  switch (btn.dataset.demo) {
    case 'streak-up':    OsMotion.celebrateStreak(current + 1, undefined, flame); break;
    case 'streak-tier':  OsMotion.celebrateStreak(7, 6, flame); break;
    case 'streak-big':   OsMotion.celebrateStreak(21, 20, flame); break;
    case 'streak-reset': OsMotion.setStreak(6, flame); break;
    case 'streak-risk':
      if (flame) flame.classList.toggle('is-at-risk');
      break;
    case 'notify':       OsMotion.notify(); break;
    case 'notify-3':     OsMotion.notify((parseInt(
                           document.querySelector('[data-bell]').dataset.count, 10) || 0) + 3); break;
    case 'notify-clear': OsMotion.setBell(0); break;
  }
});
</script>
@endsection
