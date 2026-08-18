<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('public.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('website')) {           // honeypot
            return back()->with('status', 'Message sent.');
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        try {
            Mail::to(config('mail.contact_to', config('mail.from.address')))
                ->send(new ContactMessage($data));
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors([
                'message' => 'We could not send that just now. Please email us directly.',
            ])->withInput();
        }

        return back()->with('status', 'Thank you — your message has been sent.');
    }
}
