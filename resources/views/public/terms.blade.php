@extends('layouts.app')
@section('title', 'Terms and conditions — StellarTech Explorers Club')

@section('content')
<div class="page">
  <div class="wrap page-narrow">
    <h1>Terms and conditions</h1>
    <p class="updated">Last updated {{ now()->format('F Y') }}</p>

    <div class="alert alert-warn">
      This is a working draft prepared for a student organisation. Have it reviewed by a
      responsible adult before relying on it.
    </div>

    <div class="prose">
      <h2>Using this website</h2>
      <p>You may read and share our public pages. You may not attempt to access member areas you
         are not authorised for, probe the site for vulnerabilities, scrape it automatically, or
         use it to distribute anything unlawful.</p>

      <h2>Membership</h2>
      <p>Membership is by application and at the founder's discretion. Approved members receive a
         personal account. You are responsible for keeping your password private and must not
         share your account. Tell us immediately if you think someone else has access to it.</p>

      <h2>Conduct</h2>
      <p>We expect members to be honest about their work, respectful to each other, careful with
         shared equipment, and truthful in what they log and claim. Harassment, plagiarism,
         falsifying work records and misusing club resources are grounds for removal.</p>

      <h2>Your work and who owns it</h2>
      <p>You keep authorship of what you personally create. By contributing to a club project you
         grant the club permission to use, document and display that work in connection with the
         project, with credit to you. Work built collaboratively belongs to the project team
         collectively. If you leave, you may continue to reference your contributions.</p>

      <h2>Photographs and media</h2>
      <p>Uploading media to the club means you confirm you have the right to share it and that
         anyone identifiable in it has agreed. The founder decides what is published. You can
         ask for anything featuring you to be removed.</p>

      <h2>Credits</h2>
      <p>Credits are an internal record of contribution. They are not money, have no cash value,
         cannot be bought, sold or transferred, and are not refundable. We may adjust or reset
         balances where they have been awarded in error.</p>

      <h2>Confidential projects</h2>
      <p>Some programmes, including Ultimate Rakhandar, involve confidential material. Where you
         are given access, you must not share site details, scan data, internal records or files
         outside the authorised group, during or after your membership.</p>

      <h2>Accuracy</h2>
      <p>We describe our projects honestly, with stage labels showing what has and has not been
         demonstrated. Nothing on this site is a professional structural, engineering or safety
         assessment, and it should not be relied on as one.</p>

      <h2>Ending membership</h2>
      <p>You may leave at any time. We may suspend or remove an account for breaching these terms.
         Where practical we will explain why.</p>

      <h2>Changes</h2>
      <p>We may update these terms. Material changes will be notified to members by email.</p>

      <h2>Contact</h2>
      <p>{{ setting('contact_email') }}</p>
    </div>
  </div>
</div>
@endsection
