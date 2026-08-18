@extends('layouts.app')
@section('title', 'Dashboard — StellarTech Explorers Club')
@push('meta')<meta name="robots" content="noindex, nofollow">@endpush

@section('content')
<div class="dash">
  <div class="wrap">

    <div class="dash-head">
      <div>
        <h1>Hello, {{ Str::before($user->name, ' ') }}.</h1>
        <span class="dash-role">{{ $user->role->name }}</span>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-ghost" type="submit">Sign out</button>
      </form>
    </div>

    @include('partials.alerts')

    <div class="tiles">
      <div class="glass tile">
        <span class="n">{{ $user->streak?->current_streak ?? 0 }}</span>
        <span class="l">Day streak</span>
      </div>
      <div class="glass tile accent">
        <span class="n">{{ $user->credit?->balance ?? 0 }}</span>
        <span class="l">Credits</span>
      </div>
      <div class="glass tile">
        <span class="n">{{ $tasks->count() }}</span>
        <span class="l">Open tasks</span>
      </div>
      @if (!is_null($pendingApplications))
        <a class="glass tile accent" href="{{ route('manage.applications') }}" style="display:block">
          <span class="n">{{ $pendingApplications }}</span>
          <span class="l">Applications waiting</span>
        </a>
      @endif
    </div>

    <div class="panels">
      <div class="glass panel">
        <h2>Your tasks</h2>
        @forelse ($tasks as $task)
          <div class="trow">
            <span class="st st-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span>
            <span>{{ $task->title }}</span>
            @if ($task->due_date)
              <span class="due">{{ $task->due_date->format('j M') }}</span>
            @endif
          </div>
        @empty
          <div class="empty">
            No tasks assigned yet. Your project lead will assign work as teams form.
          </div>
        @endforelse
      </div>

      <div class="glass panel">
        <h2>Announcements</h2>
        @forelse ($announcements as $a)
          <div class="trow" style="display:block">
            <strong style="display:block;font-size:.92rem;margin-bottom:.2rem">{{ $a->title }}</strong>
            <span style="font-size:.84rem;color:var(--ink-soft)">
              {{ Str::limit(strip_tags($a->body), 110) }}
            </span>
          </div>
        @empty
          <div class="empty">Nothing posted yet.</div>
        @endforelse
      </div>
    </div>

    <div class="glass panel" style="margin-top:1rem">
      <h2>Coming next to your dashboard</h2>
      <p style="font-size:.9rem;color:var(--ink-soft);margin:0">
        Work logging, reflections, media uploads and the credits ledger are being built.
        Nothing here shows placeholder data — these tiles read from the database, so they
        stay at zero until there is something real to count.
      </p>
    </div>

  </div>
</div>
@endsection
