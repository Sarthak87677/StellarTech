<x-mail::message>
# Thank you, {{ $application->name }}

We have received your application to join **StellarTech Explorers Club**.

Every application is read by a person, so give us a little time. If we move forward,
you will receive a second email with your own private account for the member workspace.

**What you told us**

- Interest area: {{ ucfirst($application->interest_area) }}
@if ($application->project_preference)
- Project preference: {{ $application->project_preference }}
@endif

If anything above is wrong, just reply to this email.

<x-mail::button :url="config('app.url')">
Visit the site
</x-mail::button>

StellarTech Explorers Club
</x-mail::message>
