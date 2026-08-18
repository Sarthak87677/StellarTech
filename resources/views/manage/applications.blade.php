@extends('layouts.app')
@section('title', 'Applications — StellarTech Explorers Club')
@push('meta')<meta name="robots" content="noindex, nofollow">@endpush

@section('content')
<div class="dash">
  <div class="wrap">

    <div class="dash-head">
      <div>
        <h1>Applications</h1>
        <span class="dash-role">Founder review</span>
      </div>
      <a class="btn btn-ghost" href="{{ route('dashboard.index') }}">Back to dashboard</a>
    </div>

    @include('partials.alerts')

    <div class="tabs">
      @foreach (['pending','approved','waitlisted','rejected','all'] as $s)
        <a class="tab {{ $status === $s ? 'on' : '' }}"
           href="{{ route('manage.applications', ['status' => $s]) }}">
          {{ $s }}
          @if ($s !== 'all' && ($counts[$s] ?? 0))<span class="n">{{ $counts[$s] }}</span>@endif
        </a>
      @endforeach
    </div>

    <div class="glass" style="padding:.4rem 1rem 1rem;overflow-x:auto">
      <table class="tbl">
        <thead>
          <tr>
            <th>Applicant</th><th>Interest</th><th>Why they applied</th>
            <th>Received</th><th>Decision</th>
          </tr>
        </thead>
        <tbody>
        @forelse ($applications as $a)
          <tr>
            <td>
              <strong>{{ $a->name }}</strong><br>
              <span style="font-size:.8rem;color:var(--ink-mute)">{{ $a->email }}</span>
              @if ($a->school)
                <br><span style="font-size:.78rem;color:var(--ink-mute)">
                  {{ $a->school }}{{ $a->grade ? ' · ' . $a->grade : '' }}</span>
              @endif
              @if ($a->parental_consent)
                <br><span class="chip planned" style="margin-top:.4rem">Parental consent</span>
              @endif
            </td>
            <td>{{ ucfirst($a->interest_area) }}</td>
            <td style="max-width:340px">{{ Str::limit($a->motivation, 220) }}</td>
            <td style="white-space:nowrap;font-family:var(--mono);font-size:.75rem">
              {{ $a->created_at->format('j M Y') }}
            </td>
            <td style="min-width:230px">
              @if ($a->status === 'pending')
                @can('decide-applications')
                <form method="POST" action="{{ route('founder.applications.decide', $a) }}">
                  @csrf
                  <select name="role" class="tab" style="width:100%;margin-bottom:.4rem;padding:.45rem">
                    <option value="member">Member</option>
                    <option value="oc_member">OC Member</option>
                    <option value="selected_oc">Selected OC</option>
                  </select>
                  <input type="text" name="note" placeholder="Note (optional)"
                         style="width:100%;margin-bottom:.5rem;padding:.45rem;border-radius:6px;
                                background:rgba(6,10,22,.7);border:1px solid var(--line-soft);
                                color:var(--ink);font-size:.8rem">
                  <div style="display:flex;gap:.35rem;flex-wrap:wrap">
                    <button class="btn btn-primary" name="decision" value="approved"
                            style="padding:.45rem .8rem;font-size:.78rem">Approve</button>
                    <button class="btn" name="decision" value="waitlisted"
                            style="padding:.45rem .8rem;font-size:.78rem">Waitlist</button>
                    <button class="btn btn-ghost" name="decision" value="rejected"
                            style="padding:.45rem .8rem;font-size:.78rem">Decline</button>
                  </div>
                </form>
                @else
                  <span style="font-size:.82rem;color:var(--ink-mute)">Founder decision required</span>
                @endcan
              @else
                <span class="chip {{ $a->status === 'approved' ? 'active' : 'planned' }}">
                  {{ $a->status }}
                </span>
                @if ($a->reviewed_at)
                  <div style="font-size:.74rem;color:var(--ink-mute);margin-top:.4rem">
                    {{ $a->reviewed_at->format('j M Y') }}
                  </div>
                @endif
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:2.5rem;color:var(--ink-mute)">
            No {{ $status === 'all' ? '' : $status }} applications.
          </td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1.4rem">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
