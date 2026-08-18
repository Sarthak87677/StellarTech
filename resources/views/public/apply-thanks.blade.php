@extends('layouts.app')
@section('title', 'Application received — StellarTech Explorers Club')
@push('meta')<meta name="robots" content="noindex">@endpush

@section('content')
<div class="page">
  <div class="wrap page-narrow" style="text-align:center">
    <div class="eyebrow" style="justify-content:center">Application received</div>
    <h1>Thank you.</h1>
    <p class="lede" style="margin:0 auto 2rem">
      Your application is with the founder. We have sent a confirmation to the email address
      you gave us — check your spam folder if it does not arrive within a few minutes.
    </p>
    <p class="lede" style="margin:0 auto 2.5rem">
      If we move forward, you will receive a second email with your own account for the
      member workspace.
    </p>
    <a class="btn btn-primary" href="{{ route('home') }}">Back to the homepage</a>
  </div>
</div>
@endsection
