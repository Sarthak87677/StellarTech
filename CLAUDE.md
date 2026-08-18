# StellarTech Explorers Club — project context

Read this before doing anything. It is the standing context for this repository.

## What this is

Official website + private club operating system for **StellarTech Explorers Club**,
a student-led robotics / AI / space science / heritage-technology club.
Live at https://stellartechexplorers.com on Hostinger shared hosting.

The founder is a beginner. Explain things simply and say exactly what to click or type.

## Hard rules

- **Never ask for** passwords, database credentials, SMTP passwords, API keys, SSH
  passwords, OTP codes, secret passes, or `.env` contents. Reference env vars by NAME.
- **Never invent** achievements, statistics, testimonials, team numbers, or completed
  results. Use honest placeholders and stage labels.
- **Never put secrets** in JavaScript, Blade templates, or anything under `public/`.
- Ask before each major stage. Do not restructure the project unprompted.

## Stack

Laravel 12 · PHP 8.3.31 · MariaDB 11.8.8 · Composer 2.9.8
Hostinger Premium shared hosting · SSH port 65002

**No Node, no npm on the server. There is no build step and we are not adding one.**
CSS and JS are plain files in `public/assets/`. Three.js loads from cdnjs r128.
Cache busting uses `?v={ASSET_VERSION}` from `.env` via `config('app.asset_version')`.

## Deployment quirk — read twice

No document-root setting on this plan, so the site uses the split-public method:

```
~/domains/stellartechexplorers.com/app/          ← this repo
~/domains/stellartechexplorers.com/public_html/  ← the served web root
```

`public_html/index.php` is patched to require `../app/vendor/autoload.php` and
`../app/bootstrap/app.php`.

**Editing `public/` changes nothing on the live site until it is copied across:**

```bash
cp -r public/assets/. ../public_html/assets/
```

Or run `./deploy.sh`. Remind the founder of this **every time** you touch a CSS, JS,
or image file. Hours have been lost to it.

## Roles

`member` → `oc_member` → `selected_oc` → `founder`

Checks compare the **slug**, never the numeric level. Authorization is server-side on
every route and every file download. Hiding a menu item is not access control.

## Ultimate Rakhandar — CONFIDENTIAL

Heritage-preservation AGV programme. A ground rover scans structures and builds a
documented condition record so change becomes measurable.

`PageController::rakhandar()` **deliberately queries nothing**. The public page renders
curated static content. No query means no query bug can leak scan data. Keep it that way.

**Never public:** hardware model numbers, monument names or locations, raw scans, point
clouds, digital twins, crack or thermal results, reports, field logs, drone
authorisation records, access logs.

Private access requires all four gates: active account → correct role → hashed shared
pass → single-use email OTP → 15-minute elevated session bound to the session ID and
re-verified on every request including downloads.

Every capability carries an honest stage label. Do not claim live autonomy, live SLAM,
or validated detection accuracy.

## Design system

```
--void #04060D   --deep #080D1C   --navy #0D1530
--plat #E8EEF6 (primary, matches the logo)   --blue #4DA8FF   --violet #8A6BFF
```

Archivo (display) · Inter (body) · JetBrains Mono (labels, status chips)
Status chips: `planned` `prototype` `testing` `active` `future module`

Hero is a Three.js solar system using **real astronomical data** — actual radii,
semi-major axes, axial tilts and sidereal periods, compressed for display. Camera sits
below the ecliptic looking up so orbits render as arcs sweeping toward the sun.

**The logo is a flat DOM image. It never enters the WebGL scene and never rotates.**

Motion: word-split headlines flying in from six directions, staged hero entrance,
directional scroll reveals, hero parallax. All disabled under `prefers-reduced-motion`.

`space.js` declares `const reduceSpace`; `app.js` declares `const reduce`. They share
global scope — **the names must stay different** or the second file dies silently.

## Database — 24 tables

identity (`roles` `users` `sessions` `otp_codes` `access_logs`) · `applications` ·
projects (`projects` `project_updates` `tasks` `monuments`) ·
media (`media_items` `media_approvals`) ·
engagement (`streak_logs` `user_streaks` `reflections` `credits`
`credit_transactions` `badges` `badge_user`) ·
rakhandar (`rakhandar_scans` `rakhandar_reports` `rakhandar_files`) ·
system (`announcements` `settings`)

Feature flags in `settings`, all seeded **off**: `payments_enabled`, `razorpay_enabled`,
`drone_module`, `leaderboard_visible`.

`MediaItem::publiclyVisible()` requires three conditions — approved, public, and
`public_path` actually set. A null `public_path` means it was never published,
whatever the status column claims.

## What works

10 public pages · application form with honeypot, timing check and rate limit ·
founder approval creating accounts in one transaction · login with lockout ·
member dashboard · 4 transactional emails · server-side cached space news feed

## What is not built

Routes sit **commented out** at the bottom of `routes/web.php` as the planned shape.
Uncomment each group as its controller is written.

Media upload/approval · task board · streak logging · reflections · credits ledger UI ·
Rakhandar Command Centre screens · password reset · events module

## Constraints that are not code problems

- **Payments** need KYC and an adult account holder. Flags and `.env` slots are ready.
- **Credits stay non-monetary.** Earned by contribution only. If money can buy them,
  money buys standing in the club and the progress system loses meaning.
- **Member-to-member chat** involves minors — needs moderation, reporting and a
  safeguarding plan before any code.
- **20 GB disk.** Prefer embedding video over self-hosting.
- **Privacy Policy and Terms are drafts** and still need adult review.
