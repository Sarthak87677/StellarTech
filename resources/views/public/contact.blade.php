@extends('layouts.app')
@section('title', 'Contact — StellarTech Explorers Club')

@section('content')
<div class="page">
  <div class="wrap page-narrow">
    <div class="eyebrow">Get in touch</div>
    <h1>Contact us.</h1>
    <p class="lede" style="margin-bottom:2.5rem">
      Questions about membership, collaboration or the Rakhandar programme. We read
      everything and reply as quickly as we can.
    </p>

    @include('partials.alerts')

    <form method="POST" action="{{ route('contact.store') }}" class="glass" style="padding:2rem 1.9rem">
      @csrf
      <div class="hp" aria-hidden="true">
        <input type="text" name="website" tabindex="-1" autocomplete="off">
      </div>

      <div class="row2">
        <div class="field @error('name') bad @enderror">
          <label for="name">Your name <span class="req">*</span></label>
          <input id="name" name="name" value="{{ old('name') }}" required maxlength="120">
          @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field @error('email') bad @enderror">
          <label for="email">Your email <span class="req">*</span></label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required>
          @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="field @error('subject') bad @enderror">
        <label for="subject">Subject <span class="req">*</span></label>
        <input id="subject" name="subject" value="{{ old('subject') }}" required maxlength="190">
        @error('subject')<div class="err">{{ $message }}</div>@enderror
      </div>

      <div class="field @error('message') bad @enderror">
        <label for="message">Message <span class="req">*</span></label>
        <textarea id="message" name="message" required minlength="20"
                  maxlength="3000">{{ old('message') }}</textarea>
        @error('message')<div class="err">{{ $message }}</div>@enderror
      </div>

      <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center">
        Send message
      </button>
    </form>

    <p style="margin-top:2rem;font-size:.9rem;color:var(--ink-soft)">
      Or email us directly at
      <a href="mailto:{{ setting('contact_email') }}" style="color:var(--blue)">{{ setting('contact_email') }}</a>.
    </p>
  </div>
</div>
@endsection
