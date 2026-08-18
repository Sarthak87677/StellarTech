@extends('layouts.app')
@section('title', $project->title . ' — StellarTech Explorers Club')
@section('description', $project->summary)

@section('content')
<div class="page">
  <div class="wrap page-narrow">
    <div class="eyebrow">{{ ucfirst($project->discipline) }}</div>
    <h1>{{ $project->title }}</h1>
    <div style="margin:1.2rem 0 2rem">
      <span class="chip {{ $project->stage }}">{{ $project->stageLabel() }}</span>
      @if ($project->started_at)
        <span style="font-family:var(--mono);font-size:.7rem;color:var(--ink-mute);margin-left:.8rem">
          Started {{ $project->started_at->format('M Y') }}
        </span>
      @endif
    </div>

    <p class="lede">{{ $project->summary }}</p>

    @if ($project->description)
      <div class="prose" style="margin-top:2rem">{!! nl2br(e($project->description)) !!}</div>
    @endif

    @if ($project->updates->isNotEmpty())
      <h2 style="font-size:1.3rem;margin:3rem 0 1.2rem">Project updates</h2>
      @foreach ($project->updates as $update)
        <div class="glass" style="padding:1.4rem 1.5rem;border-radius:12px;margin-bottom:1rem">
          <div style="font-family:var(--mono);font-size:.65rem;letter-spacing:.14em;
                      text-transform:uppercase;color:var(--blue);margin-bottom:.5rem">
            {{ $update->update_date->format('j M Y') }}
          </div>
          <h3 style="margin-bottom:.6rem">{{ $update->title }}</h3>
          <p style="font-size:.92rem;color:var(--ink-soft);margin:0">{!! nl2br(e($update->body)) !!}</p>
        </div>
      @endforeach
    @endif

    <div style="margin-top:2.5rem">
      <a class="btn btn-ghost" href="{{ route('projects') }}">← All projects</a>
    </div>
  </div>
</div>
@endsection
