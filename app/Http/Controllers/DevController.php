<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Internal build tools. Founder only, and gated by middleware on the
 * route — this controller is not an access check on its own.
 *
 * These pages exist to look at shell components in isolation before they
 * are wired into the real top bar. Nothing here reads member data.
 */
class DevController extends Controller
{
    public function motion(): View
    {
        return view('dev.motion');
    }
}
