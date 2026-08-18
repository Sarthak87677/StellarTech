<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Only the founder decides applications. Selected OC can view the queue
        // but cannot approve — separating viewing from acting is deliberate.
        Gate::define('decide-applications', fn ($user) => $user->isFounder());

        Gate::define('approve-media', fn ($user) => $user->hasRole('founder', 'selected_oc'));

        Gate::define('manage-credits', fn ($user) => $user->isFounder());

        Gate::define('access-rakhandar', fn ($user) => $user->mayAccessRakhandar());
    }
}
