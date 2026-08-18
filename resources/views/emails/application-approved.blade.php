<x-mail::message>
# Welcome to StellarTech, {{ $application->name }}

Your application was approved and your member account is ready.

**Sign in with**

- Email: {{ $application->email }}
- Temporary password: **{{ $temporaryPassword }}**

<x-mail::panel>
Change this password the first time you sign in. This email is the only copy —
we store the password only as a hash and cannot recover it for you.
</x-mail::panel>

<x-mail::button :url="route('login')">
Sign in
</x-mail::button>

Once you are in, pick up a task, log your first work session, and introduce yourself
to your project team.

StellarTech Explorers Club
</x-mail::message>
