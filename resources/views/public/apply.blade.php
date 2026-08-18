@extends('layouts.app')
@section('title', 'Apply to join — StellarTech Explorers Club')
@section('description', 'Apply for membership of StellarTech Explorers Club. No prior experience required.')

@section('content')
<div class="page">
  <div class="wrap page-narrow">
    <div class="eyebrow">Membership application</div>
    <h1>Apply to join.</h1>
    <p class="lede" style="margin-bottom:2.5rem">
      Every application is read by a person. There is no test and no prior experience
      requirement — tell us honestly what interests you and what you would like to build.
    </p>

    @include('partials.alerts')

    <form method="POST" action="{{ route('apply.store') }}" class="glass" style="padding:2rem 1.9rem">
      @csrf
      <input type="hidden" name="t" value="{{ time() }}">
      <div class="hp" aria-hidden="true">
        <label>Leave this empty</label>
        <input type="text" name="website" tabindex="-1" autocomplete="off">
      </div>

      <div class="row2">
        <div class="field @error('name') bad @enderror">
          <label for="name">Full name <span class="req">*</span></label>
          <input id="name" name="name" value="{{ old('name') }}" required maxlength="120">
          @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field @error('email') bad @enderror">
          <label for="email">Email <span class="req">*</span></label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required>
          @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row2">
        <div class="field">
          <label for="phone">Phone (optional)</label>
          <input id="phone" name="phone" value="{{ old('phone') }}" maxlength="32">
        </div>
        <div class="field">
          <label for="school">School (optional)</label>
          <input id="school" name="school" value="{{ old('school') }}" maxlength="190">
        </div>
      </div>

      <div class="row2">
        <div class="field">
          <label for="grade">Year or grade (optional)</label>
          <input id="grade" name="grade" value="{{ old('grade') }}" maxlength="32">
        </div>
        <div class="field @error('interest_area') bad @enderror">
          <label for="interest_area">Main interest <span class="req">*</span></label>
          <select id="interest_area" name="interest_area" required>
            <option value="">Choose one…</option>
            @foreach (['robotics' => 'Robotics & ground systems',
                       'ai' => 'Artificial intelligence & vision',
                       'space' => 'Space science',
                       'engineering' => 'Engineering & fabrication',
                       'heritage' => 'Heritage technology'] as $v => $label)
              <option value="{{ $v }}" @selected(old('interest_area') === $v)>{{ $label }}</option>
            @endforeach
          </select>
          @error('interest_area')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="field @error('motivation') bad @enderror">
        <label for="motivation">Why do you want to join? <span class="req">*</span></label>
        <textarea id="motivation" name="motivation" required minlength="40"
                  maxlength="2000">{{ old('motivation') }}</textarea>
        <div class="hint">A couple of honest sentences beats a polished paragraph.</div>
        @error('motivation')<div class="err">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="skills">What can you already do? (optional)</label>
        <textarea id="skills" name="skills" style="min-height:90px"
                  maxlength="1000">{{ old('skills') }}</textarea>
        <div class="hint">Coding, CAD, soldering, writing, photography, organising — anything counts.</div>
      </div>

      <div class="field">
        <label for="project_preference">Project you would like to work on (optional)</label>
        <input id="project_preference" name="project_preference"
               value="{{ old('project_preference') }}" maxlength="190">
      </div>

      <div class="field @error('consent') bad @enderror">
        <label class="check">
          <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
          <span>I consent to StellarTech Explorers Club storing this application in order to
                consider my membership. <span class="req">*</span></span>
        </label>
        @error('consent')<div class="err">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="check">
          <input type="checkbox" name="parental_consent" value="1" @checked(old('parental_consent'))>
          <span>I am under 18 and a parent or guardian has agreed to this application.</span>
        </label>
        <div class="hint">If you are under 18, please tick this after speaking with a parent or guardian.</div>
      </div>

      <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center">
        Send application
      </button>

      <p style="font-size:.78rem;color:var(--ink-mute);margin:1.2rem 0 0;text-align:center">
        We use your details only to consider this application and contact you about it.
        See our <a href="{{ route('privacy') }}" style="color:var(--blue)">privacy policy</a>.
      </p>
    </form>
  </div>
</div>
@endsection
