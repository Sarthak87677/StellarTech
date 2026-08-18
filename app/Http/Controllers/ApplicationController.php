<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceived;
use App\Models\Application;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function create(): View
    {
        abort_unless(Setting::enabled('applications_open'), 404);

        return view('public.apply');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Setting::enabled('applications_open'), 404);

        // Honeypot: bots fill hidden fields, humans never see them.
        if ($request->filled('website')) {
            return redirect()->route('apply')->with('status', 'Application received.');
        }

        // Timing check: a genuine person takes more than three seconds to read and fill this.
        if ((int) $request->input('t', 0) > 0 && (time() - (int) $request->input('t')) < 3) {
            return back()->withErrors(['name' => 'That was submitted too quickly. Please try again.'])
                         ->withInput();
        }

        $data = $request->validate([
            'name'                => ['required', 'string', 'min:2', 'max:120'],
            'email'               => ['required', 'email', 'max:190'],
            'phone'               => ['nullable', 'string', 'max:32'],
            'school'              => ['nullable', 'string', 'max:190'],
            'grade'               => ['nullable', 'string', 'max:32'],
            'interest_area'       => ['required', 'in:robotics,ai,space,engineering,heritage'],
            'motivation'          => ['required', 'string', 'min:40', 'max:2000'],
            'skills'              => ['nullable', 'string', 'max:1000'],
            'project_preference'  => ['nullable', 'string', 'max:190'],
            'consent'             => ['accepted'],
            'parental_consent'    => ['nullable', 'boolean'],
        ], [
            'motivation.min' => 'Please tell us a little more — at least a couple of sentences.',
            'consent.accepted' => 'We need your consent to store this application.',
        ]);

        // One open application per email at a time.
        $existing = Application::where('email', $data['email'])
            ->where('status', 'pending')->exists();

        if ($existing) {
            return back()->withErrors([
                'email' => 'You already have an application under review. We will be in touch.',
            ])->withInput();
        }

        $application = Application::create($data + [
            'consent'          => true,
            'parental_consent' => $request->boolean('parental_consent'),
            'status'           => 'pending',
            'ip'               => $request->ip(),
        ]);

        // Mail failures must not lose the application — it is already saved.
        try {
            Mail::to($application->email)->send(new ApplicationReceived($application));
            Mail::to(config('mail.contact_to', config('mail.from.address')))
                ->send(new ApplicationReceived($application, forFounder: true));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('apply.thanks');
    }

    public function thanks(): View
    {
        return view('public.apply-thanks');
    }
}
