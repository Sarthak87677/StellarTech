<x-mail::message>
# New application

**{{ $application->name }}** applied to join.

- Email: {{ $application->email }}
@if ($application->phone)
- Phone: {{ $application->phone }}
@endif
@if ($application->school)
- School: {{ $application->school }} {{ $application->grade ? '(' . $application->grade . ')' : '' }}
@endif
- Interest: {{ ucfirst($application->interest_area) }}
- Parental consent: {{ $application->parental_consent ? 'Given' : 'Not indicated' }}

**Why they want to join**

{{ $application->motivation }}

@if ($application->skills)
**Skills**

{{ $application->skills }}
@endif

<x-mail::button :url="route('manage.applications')">
Review application
</x-mail::button>
</x-mail::message>
