@extends('layouts.app')
@section('title', 'About — StellarTech Explorers Club')
@section('description', 'How StellarTech Explorers Club works: our mission, structure, standards and ethics.')

@section('content')
<div class="page">
  <div class="wrap">
    <div class="eyebrow">About the club</div>
    <h1 style="max-width:16ch">Engineering practice,<br>not a hobby club.</h1>

    <div class="prose" style="margin-top:2.5rem">
      <p>StellarTech Explorers Club is a student-led engineering organisation. We work across
         robotics, artificial intelligence, space science and heritage-preservation technology,
         and we run projects the way a professional lab would: research, design, build, test in
         the field, document what happened, publish honestly.</p>

      <h2>What we actually do</h2>
      <p>Members join a project team and take on real tasks. Work is logged with the hours spent
         and the evidence produced. Peers review it. Everyone writes short reflections on what
         they learned, including what failed. That last part is not decoration — a project where
         nothing went wrong is usually a project where nobody looked closely.</p>

      <h2>How we describe our work</h2>
      <p>Every capability on this site carries a stage label: <strong>planned</strong>,
         <strong>prototype</strong>, <strong>testing</strong>, <strong>active</strong> or
         <strong>future module</strong>. Nothing is called finished until it has been demonstrated
         and documented. We would rather be trusted than impressive.</p>

      <h2>Structure</h2>
      <ul>
        <li><strong>Founder</strong> — sets direction, approves membership, holds final responsibility.</li>
        <li><strong>Selected OC</strong> — senior committee members with access to restricted programmes.</li>
        <li><strong>OC Members</strong> — run project boards, assign work and review submissions.</li>
        <li><strong>Members</strong> — contribute to project teams and log their work.</li>
      </ul>
      <p>Roles are earned through consistent contribution, not appointed on day one.</p>

      <h2>Ethics</h2>
      <p>Our heritage work touches structures that belong to everyone and outlast all of us.
         We document rather than intervene, we operate only with permission, and we do not
         publish site locations or scan data. Where a finding matters, it goes to the people
         responsible for the structure — not to a social media post.</p>

      <h2>Joining</h2>
      <p>Membership is by application and every application is read by a person. There is no
         entrance test and no prior experience requirement. Curiosity and follow-through matter
         more than what you already know.</p>
    </div>

    <div style="margin-top:3rem;display:flex;gap:.8rem;flex-wrap:wrap">
      <a class="btn btn-primary" href="{{ route('apply') }}">Apply to join</a>
      <a class="btn" href="{{ route('rakhandar') }}">Ultimate Rakhandar</a>
    </div>
  </div>
</div>
@endsection
