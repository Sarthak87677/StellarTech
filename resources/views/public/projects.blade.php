@extends('layouts.app')
@section('title', 'Projects — StellarTech Explorers Club')
@section('description', 'Robotics, AI, space science and heritage technology projects run by StellarTech Explorers Club.')

@section('content')
<div class="page">
  <div class="wrap">
    <div class="eyebrow">Our work</div>
    <h1>Projects.</h1>
    <p class="lede" style="margin:1.4rem 0 3rem">
      Each project carries the stage it has actually reached. Nothing is listed as complete
      until it has been demonstrated and documented.
    </p>

    @forelse ($projects as $discipline => $group)
      <h2 style="font-size:1.3rem;margin:2.5rem 0 1.2rem;text-transform:capitalize">
        {{ $discipline }}
      </h2>
      <div class="disc">
        @foreach ($group as $project)
          <a class="disc-card" href="{{ route('project', $project) }}">
            <h3>{{ $project->title }}</h3>
            <p>{{ $project->summary }}</p>
            <span class="chip {{ $project->stage }}">{{ $project->stageLabel() }}</span>
          </a>
        @endforeach
      </div>
    @empty
      <div class="glass" style="padding:3rem 2rem;text-align:center;border-radius:16px">
        <h3 style="margin-bottom:.8rem">No public projects yet</h3>
        <p style="color:var(--ink-soft);max-width:48ch;margin:0 auto 1.5rem">
          Project pages are published by the founder once a project has enough documented work
          to be worth reading. Rather than fill this page with placeholders, we have left it
          honest.
        </p>
        <a class="btn" href="{{ route('rakhandar') }}">Read about Ultimate Rakhandar</a>
      </div>
    @endforelse
  </div>
</div>
@endsection
