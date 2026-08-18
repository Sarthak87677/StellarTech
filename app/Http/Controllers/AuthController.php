<?php

namespace App\Http\Controllers;

use App\Services\AccessLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('email', $data['email'])->first();

        // Account lockout — checked before the password so brute force
        // cannot keep testing passwords against a locked account.
        if ($user && $user->isLocked()) {
            AccessLogger::denied($user, 'login', $request, ['why' => 'locked']);
            throw ValidationException::withMessages([
                'email' => 'This account is temporarily locked. Try again later.',
            ]);
        }

        if (! $user || ! Auth::attempt($data, $request->boolean('remember'))) {
            if ($user) {
                $user->increment('failed_attempts');
                if ($user->failed_attempts >= 5) {
                    $user->update(['locked_until' => now()->addMinutes(15), 'failed_attempts' => 0]);
                }
            }
            AccessLogger::denied($user, 'login', $request, ['why' => 'bad_credentials']);

            // Deliberately generic: never reveal whether the email exists.
            throw ValidationException::withMessages([
                'email' => 'Those details do not match our records.',
            ]);
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            AccessLogger::denied($user, 'login', $request, ['why' => 'status_' . $user->status]);

            throw ValidationException::withMessages([
                'email' => $user->status === 'pending'
                    ? 'Your account is awaiting approval.'
                    : 'This account is not currently active.',
            ]);
        }

        // New session ID on login — blocks session fixation.
        $request->session()->regenerate();

        $user->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_login_at'   => now(),
            'last_login_ip'   => $request->ip(),
        ]);

        AccessLogger::success($user, 'login', $request);

        return redirect()->intended(route('dashboard.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        AccessLogger::success($request->user(), 'logout', $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been signed out.');
    }
}
