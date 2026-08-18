<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationApproved;
use App\Models\Application;
use App\Models\Credit;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStreak;
use App\Services\AccessLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApplicationReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        return view('manage.applications', [
            'applications' => Application::when($status !== 'all', fn ($q) => $q->where('status', $status))
                                ->latest()
                                ->paginate(20)
                                ->withQueryString(),
            'status' => $status,
            'counts' => Application::select('status', DB::raw('count(*) as n'))
                            ->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    /**
     * Founder decision. Approving creates the member account in the same
     * transaction as the status change — so an approved application can never
     * exist without its account, or an account without an approval record.
     */
    public function decide(Request $request, Application $application): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected,waitlisted'],
            'role'     => ['required_if:decision,approved', 'in:member,oc_member,selected_oc'],
            'note'     => ['nullable', 'string', 'max:1000'],
        ]);

        if ($application->status !== 'pending') {
            return back()->withErrors(['decision' => 'This application has already been decided.']);
        }

        $temporaryPassword = null;

        DB::transaction(function () use ($application, $data, $request, &$temporaryPassword) {

            $application->update([
                'status'      => $data['decision'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_note' => $data['note'] ?? null,
            ]);

            if ($data['decision'] !== 'approved') {
                return;
            }

            if (User::where('email', $application->email)->exists()) {
                throw new \RuntimeException('An account with that email already exists.');
            }

            // Generated, not chosen. Emailed once, and the member must change it.
            $temporaryPassword = Str::password(16);

            $user = User::create([
                'name'      => $application->name,
                'email'     => $application->email,
                'phone'     => $application->phone,
                'password'  => $temporaryPassword,
                'role_id'   => Role::where('slug', $data['role'])->value('id'),
                'status'    => 'active',
                'school'    => $application->school,
                'grade'     => $application->grade,
                'joined_at' => now(),
            ]);

            Credit::create(['user_id' => $user->id]);
            UserStreak::create(['user_id' => $user->id]);

            $application->update(['created_user_id' => $user->id]);
        });

        AccessLogger::success($request->user(), 'application_decide', $request, [
            'resource_type' => 'application',
            'resource_id'   => $application->id,
            'decision'      => $data['decision'],
        ]);

        if ($temporaryPassword) {
            try {
                Mail::to($application->email)
                    ->send(new ApplicationApproved($application, $temporaryPassword));
            } catch (\Throwable $e) {
                report($e);
                return back()->with('warning',
                    'Account created, but the email failed to send. Contact the member directly.');
            }
        }

        return back()->with('status', 'Application ' . $data['decision'] . '.');
    }
}
