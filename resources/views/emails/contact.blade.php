<x-mail::message>
# Website enquiry

**From:** {{ $data['name'] }} ({{ $data['email'] }})
**Subject:** {{ $data['subject'] }}

---

{{ $data['message'] }}

---

Reply directly to this email to respond.
</x-mail::message>
