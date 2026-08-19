<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| PUBLIC — no authentication required
|----------------------------------------------------------------------
*/
Route::get('/',                   [PageController::class, 'home'])->name('home');
Route::get('/about',              [PageController::class, 'about'])->name('about');
Route::get('/projects',           [PageController::class, 'projects'])->name('projects');
Route::get('/projects/{project}', [PageController::class, 'project'])->name('project');
Route::get('/ultimate-rakhandar', [PageController::class, 'rakhandar'])->name('rakhandar');
Route::get('/membership-credits', [PageController::class, 'credits'])->name('credits');
Route::get('/privacy',            [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms',              [PageController::class, 'terms'])->name('terms');

// Throttled at the route, not just in the controller.
Route::get('/apply',           [ApplicationController::class, 'create'])->name('apply');
Route::post('/apply',          [ApplicationController::class, 'store'])
     ->middleware('throttle:3,60')->name('apply.store');
Route::get('/apply/thank-you', [ApplicationController::class, 'thanks'])->name('apply.thanks');

Route::get('/contact',  [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
     ->middleware('throttle:3,60')->name('contact.store');

/*
|----------------------------------------------------------------------
| AUTHENTICATION
|----------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
         ->middleware('throttle:5,1')->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|----------------------------------------------------------------------
| MEMBER AREA — any active account
|----------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
});

/*
|----------------------------------------------------------------------
| REVIEW — selected OC and founder
|----------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:selected_oc,founder'])
    ->prefix('manage')->name('manage.')->group(function () {
        Route::get('/applications', [ApplicationReviewController::class, 'index'])
            ->name('applications');
    });

/*
|----------------------------------------------------------------------
| FOUNDER ONLY
|----------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:founder'])
    ->prefix('founder')->name('founder.')->group(function () {
        Route::post('/applications/{application}/decide',
            [ApplicationReviewController::class, 'decide'])->name('applications.decide');
    });

/*
|----------------------------------------------------------------------
| INTERNAL BUILD TOOLS — founder only
|
| Component previews used while building the OS shell. Gated by the same
| server-side role middleware as everything else: being an internal page
| is not a security control, the middleware is.
|----------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:founder'])
    ->prefix('dev')->name('dev.')->group(function () {
        Route::get('/motion', [DevController::class, 'motion'])->name('motion');
    });

/*
|======================================================================
| NOT YET BUILT — kept here as the planned shape, commented out so the
| app boots. Uncomment each group as its controller is written.
|
| Build order: media uploads -> tasks/streaks -> credits -> Rakhandar.
|======================================================================
|
| Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
|     Route::get('/tasks',        [TaskController::class, 'mine'])->name('tasks');
|     Route::get('/streaks',      [StreakController::class, 'index'])->name('streaks');
|     Route::post('/streaks',     [StreakController::class, 'store'])->name('streaks.store');
|     Route::get('/reflections',  [ReflectionController::class, 'index'])->name('reflections');
|     Route::get('/credits',      [CreditController::class, 'mine'])->name('credits');
|     Route::get('/media',        [MemberMediaController::class, 'index'])->name('media');
|     Route::post('/media',       [MemberMediaController::class, 'store'])
|          ->middleware('throttle:20,60')->name('media.store');
|     Route::get('/files/{media}',[MemberMediaController::class, 'stream'])->name('files.stream');
| });
|
| // ULTIMATE RAKHANDAR — four gates, stacked. The elevated session alone
| // is never sufficient: RakhandarElevated re-verifies status, role and
| // session binding on every request, including file downloads.
| Route::middleware(['auth', 'role:selected_oc,founder'])
|     ->prefix('rakhandar')->name('rakhandar.')->group(function () {
|         Route::get('/unlock',       [RakhandarAccessController::class, 'unlock'])->name('unlock');
|         Route::post('/unlock/pass', [RakhandarAccessController::class, 'verifyPass'])
|              ->middleware('throttle:5,60')->name('unlock.pass');
|         Route::post('/unlock/otp',  [RakhandarAccessController::class, 'verifyOtp'])
|              ->middleware('throttle:5,60')->name('unlock.otp');
|
|         Route::middleware(['rakhandar.elevated', 'throttle:60,1'])->group(function () {
|             Route::get('/',             [RakhandarController::class, 'index'])->name('index');
|             Route::get('/files/{file}', [RakhandarFileController::class, 'stream'])->name('file');
|         });
|     });
*/
