@extends('layouts.app')
@section('title', 'Privacy policy — StellarTech Explorers Club')

@section('content')
<div class="page">
  <div class="wrap page-narrow">
    <h1>Privacy policy</h1>
    <p class="updated">Last updated {{ now()->format('F Y') }}</p>

    <div class="alert alert-warn">
      This is a working draft prepared for a student organisation. Have it reviewed by a
      responsible adult, and by a legal professional if you collect data from minors at scale.
    </div>

    <div class="prose">
      <h2>Who we are</h2>
      <p>StellarTech Explorers Club, a student-led engineering organisation, operating
         stellartechexplorers.com. Contact: {{ setting('contact_email') }}.</p>

      <h2>What we collect</h2>
      <h3>If you apply for membership</h3>
      <p>Name, email address, and optionally phone number, school and year group. Your stated
         interests, skills and reasons for applying. Whether a parent or guardian has consented.
         Your IP address, recorded to prevent automated abuse of the form.</p>
      <h3>If you contact us</h3>
      <p>Name, email address and the content of your message.</p>
      <h3>If you become a member</h3>
      <p>Everything above, plus your account activity: tasks, logged work hours, reflections,
         uploaded files and credit history.</p>
      <h3>Automatically</h3>
      <p>Standard server logs, and a session cookie that keeps you signed in. We do not use
         analytics or tracking cookies.</p>

      <h2>Why we collect it</h2>
      <ul>
        <li>To consider your application and contact you about it</li>
        <li>To operate member accounts and record club work</li>
        <li>To keep the site secure and detect misuse</li>
      </ul>
      <p>We do not sell your data. We do not share it with third parties for marketing.</p>

      <h2>People under 18</h2>
      <p>Many of our members are minors. If you are under 18, please speak with a parent or
         guardian before applying and tick the consent box on the form. We collect the minimum
         we need. We do not publish a member's full name, school and photograph together without
         specific permission. A parent or guardian may ask us to delete their child's data at any
         time by emailing us.</p>

      <h2>Photographs and video</h2>
      <p>We publish photographs of club activity only after the founder approves them. Location
         data is removed from images before publication. If you appear in something published and
         want it removed, email us and we will remove it.</p>

      <h2>Advertising</h2>
      <p>This site may display advertising served by Google AdSense on public pages. Google may
         use cookies to serve ads based on your prior visits to this or other websites. You can
         opt out of personalised advertising in Google's Ads Settings. We do not show
         advertising on sign-in pages or anywhere inside the member area.</p>

      <h2>External services</h2>
      <p>Our homepage shows space-industry headlines sourced from the Spaceflight News API. We
         fetch these on our server and cache them, so your browser does not contact that service
         and no data about you is shared with it.</p>

      <h2>How long we keep it</h2>
      <p>Applications that are not approved are kept for twelve months, then deleted. Member data
         is kept while the account is active and for twelve months afterwards. Security logs are
         kept for twelve months.</p>

      <h2>How we protect it</h2>
      <p>Passwords are stored only as cryptographic hashes and cannot be recovered by us. The site
         runs over HTTPS. Access to member data is restricted by role, and administrative access
         is logged. Private files are stored outside the public web directory.</p>

      <h2>Your rights</h2>
      <p>You may ask us for a copy of your data, ask us to correct it, or ask us to delete it.
         Email {{ setting('contact_email') }} and we will respond within 30 days.</p>

      <h2>Changes</h2>
      <p>If we change this policy we will update the date above and, for material changes, notify
         members by email.</p>
    </div>
  </div>
</div>
@endsection
