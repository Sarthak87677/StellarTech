@extends('layouts.app')
@section('title', 'Membership credits — StellarTech Explorers Club')

@section('content')
<div class="page">
  <div class="wrap page-narrow">
    <div class="eyebrow">Membership credits</div>
    <h1>How credits work.</h1>

    <div class="alert alert-warn" style="margin-top:2rem">
      Credits are not money. They cannot be bought, sold, transferred or exchanged for cash,
      and there is no payment system on this website.
    </div>

    <div class="prose">
      <h2>What they are</h2>
      <p>Credits are an internal record of contribution. Members earn them by doing club work —
         completing tasks, logging documented research and build hours, producing documentation,
         presenting, and helping other members.</p>

      <h2>What they can be used for</h2>
      <p>We intend credits to unlock club resources: workshop places, priority on equipment,
         access to particular project work, and internal recognition. Each of these is enabled
         by the founder as the club grows.</p>

      <h2>How they are awarded</h2>
      <p>The founder adds or deducts credits manually, and every change records a reason. Members
         can see their own balance and full history in the dashboard. Nothing is awarded silently.</p>

      <h2>What we will not do</h2>
      <ul>
        <li>Sell credits, or accept payment for them</li>
        <li>Convert credits to money or refund them</li>
        <li>Use credits to rank members publicly against each other</li>
      </ul>
      <p>If a real payment system is ever introduced — for workshop fees, say — it will be set up
         properly by a responsible adult, announced clearly, and kept entirely separate from this
         contribution record.</p>
    </div>

    <div style="margin-top:2.5rem">
      <a class="btn btn-primary" href="{{ route('apply') }}">Apply to join</a>
    </div>
  </div>
</div>
@endsection
