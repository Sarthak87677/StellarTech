# StellarTech Explorers Club — Project Context

## What this is
Official website + private Club OS for **StellarTech Explorers Club**, a student-led
robotics / AI / space science / heritage-technology organisation.
Domain: `stellartechexplorers.com` (Hostinger Premium Web Hosting).

---

## Stack (decided — do not re-litigate)
- **Laravel 11**, PHP 8.3, MySQL, Blade, Vite
- **Three.js + GSAP + Lenis** for the front-end experience
- Own auth only: email + password + email OTP.
  **No** OpenAI / Google / Supabase / Firebase auth. No external identity provider.
- No Hostinger Horizons. Deploy to the existing Hostinger account.

## Confirmed hosting facts
| Item | Value |
|---|---|
| Plan | Premium Web Hosting (to 2027-07-05) |
| PHP | 8.3 active (8.2 / 8.4 / 8.5 available) |
| Extensions | ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, pdo, nd_pdo_mysql, session, tokenizer, xml, zip, phar, **gd + imagick** — all enabled |
| SSH | Active |
| Git | Available in hPanel → Advanced |
| Cron | Available |
| SSL | Lifetime SSL, active, never expires |
| DB prefix | `u390930225_` |
| Backups | Weekly only → take a manual snapshot before every deploy |
| Disk | 20 GB / 400K inodes |
| Staging | `test.stellartechexplorers.com` exists — deploy here first, `noindex` |

### Open items
- Root domain not yet connected (nameservers should be `ns1.dns-parking.com` / `ns2.dns-parking.com`)
- `ads.txt` presence in `public_html` unconfirmed — **must be preserved** at domain root
- Force HTTPS toggle state unconfirmed
- Document root editable? unconfirmed — decides deployment shape
- `director@` mailbox is on a Business Email **trial**; `connect@` not yet created

---

## Absolute rules

**Never ask the user for, or write into any file:** passwords, DB passwords, SMTP
passwords, API keys, OTP codes, secret passes, private keys, `.env` contents.
Reference them as env var names only.

**Never invent** achievements, statistics, testimonials, team numbers, or completed
results. Use honest placeholders and stage labels.

**Storage layout**
- App lives **above** the web root. Only `public/` contents are served.
- `.env` never inside `public_html`.
- Private files → `storage/app/private/` (media pending, applications, all Rakhandar data).
- Public approved media → separate public disk.
- `ads.txt` stays at the document root, byte-for-byte.
- `APP_DEBUG=false` in production. Force HTTPS.
- No AdSense units on login pages or any authenticated route.

---

## Roles
`visitor` → `member` → `oc_member` → `selected_oc` → `founder`

- **member** — own dashboard, tasks, streaks, reflections, credits, uploads (pending)
- **oc_member** — full project board, assign tasks, announcements, pre-review submissions
- **selected_oc** — media approval, view applications, **Rakhandar access**
- **founder** — everything: approve applications, assign roles, adjust credits, settings, logs

Authorization is **server-side on every route and every file download**. Hiding a menu
item is not access control.

---

## Ultimate Rakhandar — CONFIDENTIAL

Heritage-preservation AGV programme. Ground rover scans structures, builds a baseline
record on first visit, compares on later visits.

**Public page may show:** mission, high-level workflow, honest stage labels, founder-approved
media, heritage ethics statement, generic capability language ("stereo depth vision",
"thermal array", "high-resolution documentation camera").

**Never public:** hardware model numbers, monument names or locations, raw scans, point
clouds, digital twins, crack/thermal results, inspection or comparison reports, field logs,
drone authorisation records, internal plans, access logs.

The public Rakhandar page reads from **curated static content**. It must have no query path
to `rakhandar_*` tables at all.

### Private access — all four required
1. Approved logged-in account
2. Role is `founder` or `selected_oc`
3. Correct shared secret pass (per-user hash; never emailed, never logged, never displayed)
4. Single-use 6-digit email OTP, hashed at rest, 5-minute expiry, max 5 attempts

Then open a **15-minute elevated session**. Re-check all four on every private page load and
every file download — the elevated flag alone is never sufficient. Log grants *and* denials.

Stage labels required on every capability: `planned` / `prototype` / `testing` / `future module`.
Do not claim live autonomy, live SLAM, or completed results.

---

## Security baseline
CSRF on all state-changing forms · rate limiting (login 5/min, OTP request 3/10min,
apply+contact 3/hr) · account lockout · session regeneration on login and privilege change ·
30-min idle timeout · HttpOnly + Secure + SameSite cookies · upload allowlist by extension
**and** MIME sniff · size caps · randomised filenames · EXIF GPS stripped from public images ·
`.htaccess` denying PHP execution in upload dirs · deny-by-default policies ·
`access_logs` for logins, approvals, credit changes, Rakhandar access, private downloads.

---

## Database (24 tables)
`roles` `users` `sessions` `otp_codes` `access_logs` `applications` `projects`
`project_updates` `tasks` `media_items` `media_approvals` `streak_logs` `user_streaks`
`reflections` `credits` `credit_transactions` `announcements` `settings` `badges`
`badge_user` `monuments` `rakhandar_scans` `rakhandar_reports` `rakhandar_files`

Feature flags in `settings`: `payments_enabled=false` (Razorpay stubbed, not active),
`drone_module=false`, `leaderboard_visible`.

Credits are **non-monetary**. Every credit change requires a reason + admin note and writes
a `credit_transactions` row with `balance_after`.

---

## Design system
```
--void #04060D   --deep #080D1E   --navy #0D1530
--cyan #35E2F5   --violet #7A5CFF
--ink #EAF2FF    --ink-soft #A9BCD8   --ink-mute #6B7E9C
status: planned #6B7E9C · prototype #7A5CFF · testing #35E2F5 · active #3BE8A4
```
Display **Archivo** · body **Inter** · mono **JetBrains Mono** (labels, status chips, readouts).

Hero signature: point cloud of a domed structure read by a **rising scan plane** — points
brighten as the survey line passes. Grounded in what Rakhandar actually does.

**The club logo is flat SVG in the DOM, layered above the canvas. It never enters the WebGL
scene, never rotates, never distorts.**

Gate WebGL off for `prefers-reduced-motion`, viewports under 600px, or `hardwareConcurrency <= 2`
→ static gradient fallback. Pause the render loop when the hero is off screen. Cap DPR at 1.75.
Visible keyboard focus everywhere. No childish gaming-style UI.

Reference implementation of the homepage exists as a standalone `index.html` — port it to
Blade components, do not rebuild from scratch.

---

## Build order
1. **Done** — design tokens + homepage + Three.js hero (standalone HTML)
2. Laravel scaffold, `.env`, all 24 migrations + seeders, port homepage to Blade
3. Remaining public pages: About, Projects, Rakhandar overview, Contact, Privacy, Terms, Credits
4. Apply form → founder approval → account creation → approval email; auth + roles + middleware
5. Member dashboard: tasks, credits, streaks, reflections, media upload → approval → publish
6. Rakhandar: secret pass + OTP elevation, Command Centre placeholders, private file streaming
7. Security pass, staging deploy, production deploy with backup + rollback

**Phase 2 (do not build yet):** badges, leaderboard, monthly PDF reports, missed-day recovery,
events module, digital-twin/SLAM viewer, automated comparison reports, live camera feed,
rover telemetry API, Razorpay.

---

## Working style
Ask before each major stage. Don't assume facts not stated here. Use honest placeholders where
something isn't live. Deploy to `test.` before the root domain. Take a manual backup before
every production deploy.
