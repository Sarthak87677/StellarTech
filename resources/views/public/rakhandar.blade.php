{{--
  ULTIMATE RAKHANDAR — PUBLIC OVERVIEW.

  This view renders curated static content ONLY. It receives no data from the
  rakhandar_* tables and must never be given any. No monument names, no locations,
  no scan data, no reports, no hardware model numbers.
--}}
@extends('layouts.app')
@section('title', 'Ultimate Rakhandar — StellarTech Explorers Club')
@section('description', 'A heritage-preservation robotics programme using a ground rover to document the condition of historic structures over time.')

@section('content')
<div class="page">
  <div class="wrap">
    <div class="eyebrow">Flagship programme</div>
    <h1>Ultimate Rakhandar</h1>
    <p class="lede" style="margin-top:1.4rem;max-width:60ch">
      A heritage-preservation robotics programme. A ground rover carries a sensor package
      around a structure and builds a documented record of its condition — so that change
      over time becomes measurable instead of remembered.
    </p>

    <div class="prose" style="margin-top:2.5rem">
      <h2>Why it exists</h2>
      <p>Historic structures deteriorate slowly. A crack that widens two millimetres a year is
         invisible to memory and obvious to a measurement taken twice. Most sites have no such
         record. Rakhandar exists to create one, repeatably and cheaply enough that it can be
         done regularly rather than once a decade.</p>

      <h2>How a survey works</h2>
      <ul>
        <li><strong>Ground first.</strong> The rover drives a planned route around the structure,
            capturing visual and thermal data at consistent positions.
            <span class="chip testing">Testing</span></li>
        <li><strong>Surface defect detection.</strong> Stereo depth vision is used to identify
            cracking and surface change. <span class="chip prototype">Prototype</span></li>
        <li><strong>Thermal survey.</strong> A thermal array looks for moisture patterns and
            sub-surface indicators not visible to the eye.
            <span class="chip prototype">Prototype</span></li>
        <li><strong>Baseline record.</strong> The first visit to a structure creates the reference
            record everything later is compared against. <span class="chip planned">Planned</span></li>
        <li><strong>Comparison survey.</strong> Later visits are matched against the baseline to
            surface new cracking, geometric change and thermal differences.
            <span class="chip planned">Planned</span></li>
        <li><strong>Aerial pass.</strong> Considered only where ground coverage is incomplete, and
            only with explicit authorisation. <span class="chip future">Future module</span></li>
      </ul>

      <h2>Documentation</h2>
      <p>Every survey produces an inspection record. Where a baseline exists, a separate comparison
         document sets the new survey against it and states what changed. These go to the people
         responsible for the structure.</p>

      <h2>Ethics and access</h2>
      <p>We operate only with permission from the relevant authority. We document; we do not
         intervene, touch, clean or repair. Findings that could compromise a site's security are
         not published.</p>
      <p><strong>Site details, scan data, digital records and internal reports are restricted to
         authorised members.</strong> This page is a deliberate summary, and it is the full extent
         of what we publish.</p>

      <h2>Honest status</h2>
      <p>Rakhandar is an active research programme built by students. We do not claim completed
         autonomy, live mapping or validated detection accuracy. Each capability above carries the
         stage it has actually reached. When something is demonstrated and documented, the label
         changes and not before.</p>
    </div>

    <div style="margin-top:3rem;display:flex;gap:.8rem;flex-wrap:wrap">
      <a class="btn btn-primary" href="{{ route('apply') }}">Work on this project</a>
      <a class="btn" href="{{ route('contact') }}">Enquire about collaboration</a>
    </div>
  </div>
</div>
@endsection
