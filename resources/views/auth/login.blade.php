@extends('layouts.app')
@section('title', 'Member login — StellarTech Explorers Club')

@push('meta')<meta name="robots" content="noindex">@endpush

@section('content')
<div class="auth-wrap">
  <div class="glass auth-card">
    <img class="mark" src="{{ asset('images/logo-mark.png') }}" alt="">
    <h1>Member sign in</h1>
    <p class="sub">Accounts are created by the founder after an application is approved.</p>

    @include('partials.alerts')

    <form method="POST" action="{{ route('login.store') }}">
      @csrf

      <div class="field @error('email') bad @enderror">
        <label for="email">Email address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               required autocomplete="email" autofocus>
        @error('email')<div class="err">{{ $message }}</div>@enderror
      </div>

      <div class="field @error('password') bad @enderror">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
        @error('password')<div class="err">{{ $message }}</div>@enderror
      </div>

      <label class="check" style="margin-bottom:1.4rem">
        <input type="checkbox" name="remember" value="1">
        <span>Keep me signed in on this device</span>
      </label>

      <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center">
        Sign in
      </button>
    </form>

    <div class="auth-foot">
      Not a member yet? <a href="{{ route('apply') }}">Apply to join</a>
    </div>
  </div>
</div>
@endsection
