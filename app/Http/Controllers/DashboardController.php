<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Application;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.index', [
            'user' => $user->load(['role', 'credit', 'streak']),

            'tasks' => Task::where('assigned_to', $user->id)
                        ->whereNot('status', 'done')
                        ->orderByRaw("FIELD(status,'in_progress','review','todo','backlog')")
                        ->limit(6)->get(),

            'announcements' => Announcement::where('status', 'published')
                        ->whereIn('audience', $this->audiencesFor($user))
                        ->where(fn ($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
                        ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                        ->orderByDesc('pinned')->latest()->limit(4)->get(),

            // Founder-only counter; other roles get null and the tile is not rendered.
            'pendingApplications' => $user->isFounder()
                        ? Application::where('status', 'pending')->count()
                        : null,
        ]);
    }

    protected function audiencesFor($user): array
    {
        return match ($user->role->slug) {
            'founder'     => ['public', 'members', 'oc', 'founder'],
            'selected_oc',
            'oc_member'   => ['public', 'members', 'oc'],
            default       => ['public', 'members'],
        };
    }
}
