# Claude Code handoff prompt

Open a terminal in the `stellartech` folder, run `claude`, and paste everything
between the lines below.

---

```
Read docs/00-project-brief.md first, then this brief. Do not change any files until
you have audited the project and reported back.

## WHO I AM AND WHAT THIS IS

I am the founder of StellarTech Explorers Club, a student-led robotics / AI / space
science / heritage-technology club. This is our official website plus a private club
operating system. It is LIVE at https://stellartechexplorers.com on Hostinger shared
hosting. I am a beginner — explain things simply and tell me exactly what to click or
type. I have never deployed a site before this one.

## HARD RULES

- NEVER ask me for passwords, database credentials, SMTP passwords, API keys, SSH
  passwords, OTP codes, secret passes, or the contents of .env. You never need them.
  Reference environment variables by NAME only.
- NEVER invent achievements, statistics, testimonials, team numbers, or completed
  results. Use honest placeholders and stage labels.
- NEVER put secrets in JavaScript, Blade templates, or anything under public/.
- Ask me before each major stage. Do not restructure the project unprompted.

## STACK AND HOSTING REALITY

- Laravel 12, PHP 8.3.31, MariaDB 11.8.8, Composer 2.9.8
- Hostinger Premium shared hosting, server 82.25.125.132, SSH on port 65002
- NO NODE, NO NPM on the server. There is no build step and we are not adding one.
- CSS and JS are plain files in public/assets/. Three.js loads from cdnjs (r128).
- Cache busting is ?v={ASSET_VERSION} from .env, read via config('app.asset_version')

### CRITICAL DEPLOYMENT QUIRK — read twice

There is no document-root setting on this plan, so the site uses the split-public
method. The Laravel project lives at:

    ~/domains/stellartechexplorers.com/app/

and the served web root is a SEPARATE folder:

    ~/domains/stellartechexplorers.com/public_html/

public_html/index.php has been patched to require ../app/vendor/autoload.php and
../app/bootstrap/app.php.

THIS MEANS: editing anything in app/public/ changes NOTHING on the live site until it
is copied across. After any asset change you must run:

    cp -r app/public/assets/. public_html/assets/

I have lost hours to forgetting this. Remind me every single time you touch a CSS,
JS, or image file, and include the copy command in your instructions.

## WHAT ALREADY EXISTS AND WORKS

Database — 24 tables across 7 migrations, grouped by domain:
  identity (roles, users, sessions, otp_codes, access_logs), applications,
  projects (projects, project_updates, tasks, monuments),
  media (media_items, media_approvals),
  engagement (streak_logs, user_streaks, reflections, credits,
  credit_transactions, badges, badge_user),
  rakhandar (rakhandar_scans, rakhandar_reports, rakhandar_files),
  system (announcements, settings)

Seeded: 4 roles, 18 settings, 8 badges, 1 founder account (mine, active).

Roles, lowest to highest: member -> oc_member -> selected_oc -> founder
Checks compare the SLUG, never the numeric level.

Public pages, all written with real content and live:
  Home, About, Projects, Project detail, Ultimate Rakhandar overview,
  Membership Credits, Privacy Policy, Terms, Contact, Apply, Apply thank-you

Working flows:
  - Application form (honeypot + timing check + rate limit) -> saved -> two emails
  - Founder reviews at /manage/applications -> approves with role assignment ->
    account created in the SAME transaction -> temporary password emailed once
  - Login with failed-attempt lockout and deliberately generic error messages
  - Member dashboard, role-gated, all tiles read from the database
  - Space news feed on the homepage from Spaceflight News API (free, no key),
    fetched SERVER-SIDE in App\Services\NewsService and cached 15 min with a
    7-day stale fallback

Security already in place:
  - EnsureRole middleware (server-side, logs denials)
  - RakhandarElevated middleware — four gates: active account, correct role,
    hashed shared pass, single-use email OTP, then a 15-minute elevated session
    bound to the session ID and re-verified on EVERY request including downloads
  - AccessLogger service logging grants AND denials
  - CSRF, session encryption, secure cookies, 30-min idle timeout

Design system:
  --void #04060D, --deep #080D1C, --navy #0D1530
  --plat #E8EEF6 (primary, matches the logo), --blue #4DA8FF, --violet #8A6BFF
  Fonts: Archivo (display), Inter (body), JetBrains Mono (labels/status)
  Status chips: planned / prototype / testing / active / future module
  Hero: Three.js solar system with REAL astronomical data — actual radii, semi-major
  axes, axial tilts and sidereal periods, compressed for display. Camera sits below
  the ecliptic looking up so orbits render as arcs sweeping toward the sun.
  THE LOGO IS A FLAT DOM IMAGE. It must never enter the WebGL scene, never rotate.
  Motion: word-split headlines flying in from six directions, staged hero entrance,
  directional scroll reveals, hero parallax. All disabled under prefers-reduced-motion.

## ULTIMATE RAKHANDAR — CONFIDENTIAL

A heritage-preservation AGV programme. A ground rover scans historic structures and
builds a documented condition record so change becomes measurable.

The public page renders CURATED STATIC CONTENT ONLY. PageController::rakhandar()
deliberately queries nothing — no query means no query bug can leak scan data.
Keep it that way.

Never expose publicly: hardware model numbers, monument names or locations, raw
scans, point clouds, digital twins, crack or thermal results, reports, field logs,
drone authorisation records, access logs.

Every capability carries an honest stage label. Do not claim live autonomy, live
SLAM, or validated detection accuracy.

## WHAT IS NOT BUILT

Routes for these sit COMMENTED OUT at the bottom of routes/web.php as the planned
shape. Uncomment each group as its controller is written.

  - Media upload + approval queue (tables exist, no controller)
  - Task board (table exists, no controller)
  - Streak logging and reflections (tables exist, no controller)
  - Credits ledger UI (tables exist, no controller)
  - Rakhandar Command Centre screens (middleware and tables exist, no controller)
  - Password reset flow
  - Events / workshops module

## KNOWN ISSUES TO CHECK FIRST

1. public/assets/js/app.js previously had its entire motion block DUPLICATED, which
   threw "Identifier already declared" and killed all animation. It has been
   replaced. VERIFY there is exactly one occurrence of `const WORD_IN`,
   `function splitWords`, `function choreograph` and `const heads` in BOTH
   app/public/assets/js/app.js AND public_html/assets/js/app.js.

2. space.js declares `const reduceSpace`, app.js declares `const reduce`. They share
   global scope — the names must stay different or the second file dies.

3. public_html/favicon.ico was 0 bytes. Confirm it is now a real image.

4. Verify app/public/assets/ and public_html/assets/ are identical. If they have
   drifted, that is the cause of any "my change did nothing" symptom.

5. The domain previously pointed at a different host. DNS now points to
   82.25.125.132. Confirm the site loads and there is no stale routing.

6. Confirm /ads.txt exists at the web root — AdSense verification depends on it and
   it went missing during deployment.

## MY WISHLIST — I know this is large, help me sequence it

I eventually want: founder command mode (edit site content from the dashboard),
two AI assistants (Jarvis voice/text, Nova FAQ helper, both renameable),
member profiles with avatars and member-to-member chat, a courses hub with
enrolment and certificates, an e-store with "Tech Tokens" and referrals,
gamified levels and leaderboards, project uploads with folders and rankings,
a news hub and competition hub with countdowns, payments via UPI/Razorpay,
a certificates generator, mentor mode, PWA support, and an analytics dashboard.

I understand this is many months of work. DO NOT try to build it all. Tell me
honestly what each item costs in time, flag anything that needs a legal or safety
decision rather than code, and help me pick ONE thing to build properly at a time.

Constraints I already know about:
  - Payments need KYC and an adult account holder. I am arranging a parent to do
    this. The feature flags payments_enabled and razorpay_enabled are seeded OFF
    and .env already has RAZORPAY_KEY / RAZORPAY_SECRET slots waiting.
  - Credits must stay NON-MONETARY and separate from any future purchase system.
    Credits are earned by contribution. If money can buy them, money buys standing
    in the club and the whole progress system loses meaning.
  - Member-to-member chat involves minors, so it needs moderation, reporting and a
    safeguarding plan before any code.
  - Disk is 20 GB. Prefer embedding video from YouTube/Vimeo over self-hosting.
  - Privacy Policy and Terms are drafts that still need adult review.

## WHAT I WANT FROM YOU RIGHT NOW

1. Audit app/, resources/, routes/, public/assets/ and database/ and tell me what
   is broken, missing, or inconsistent — especially the six known issues above.
2. Confirm whether app/public/assets/ and public_html/assets/ match.
3. Give me a short prioritised list of what to fix or build next, with rough time
   estimates, and your recommendation for which ONE to do first.

Do not write any code yet. Audit and report.
```

---

## After it reports back

Good follow-up prompts, one at a time:

- `Fix the issues you found. Show me each change before applying it.`
- `Build the media upload and approval flow. Members upload, founder approves, approved public media appears on the homepage. Follow the existing MediaItem model and its publiclyVisible scope.`
- `Build the password reset flow using the existing otp_codes table.`
- `Build the Rakhandar Command Centre screens. The middleware and tables already exist — wire up RakhandarAccessController and RakhandarController.`

## Reminders for yourself

- Take a backup before letting it change anything: hPanel → Files → Backups →
  Generate, and phpMyAdmin → Export.
- After any CSS/JS change, bump `ASSET_VERSION` in `.env` and copy assets to
  `public_html`.
- If a change seems to do nothing, it is almost always the `public_html` copy step.
