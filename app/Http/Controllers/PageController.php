<?php

namespace App\Http\Controllers;

use App\Models\MediaItem;
use App\Models\Project;
use App\Models\User;
use App\Services\NewsService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(NewsService $news): View
    {
        return view('public.home', [
            'articles'  => $news->latest(6),

            // Only approved + public media ever reaches a visitor.
            'media'     => MediaItem::publiclyVisible()
                              ->where('is_featured_home', true)
                              ->orderBy('sort_order')
                              ->limit(9)
                              ->get(),

            'founder'   => User::founder(),
            'committee' => User::committee(),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function projects(): View
    {
        return view('public.projects', [
            'projects' => Project::publiclyVisible()
                            ->orderBy('sort_order')
                            ->get()
                            ->groupBy('discipline'),
        ]);
    }

    public function project(Project $project): View
    {
        abort_unless($project->visibility === 'public', 404);

        return view('public.project', [
            'project' => $project->load(['updates' => fn ($q) => $q->publiclyVisible()]),
        ]);
    }

    /**
     * Ultimate Rakhandar — public overview.
     *
     * CONFIDENTIAL: this action deliberately queries NOTHING from the
     * rakhandar_* tables. The page renders curated static content only.
     * A query bug here cannot leak scan data because there is no query.
     */
    public function rakhandar(): View
    {
        return view('public.rakhandar');
    }

    public function credits(): View  { return view('public.credits'); }
    public function privacy(): View  { return view('public.privacy'); }
    public function terms(): View    { return view('public.terms'); }
}
