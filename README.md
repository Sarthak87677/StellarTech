# StellarTech Explorers Club

Website and private club operating system for StellarTech Explorers Club — a
student-led robotics, AI, space science and heritage-technology club.

**Live:** https://stellartechexplorers.com
**Stack:** Laravel 12 · PHP 8.3 · MariaDB · plain CSS/JS (no build step)

---

## What this repo contains

The **custom application code** that overlays a stock Laravel 12 install. It does not
include `vendor/`, `bootstrap/`, `config/`, `composer.json` or `artisan` — those come
from Laravel itself and are installed with Composer.

```
app/          models, controllers, middleware, mail, services
database/     7 migrations (24 tables), 5 seeders
resources/    Blade views — 10 public pages, auth, dashboard, admin, emails
routes/       web.php — the security shape of the whole site
public/       plain CSS and JS assets, logo images
docs/         setup guides, deployment, payments notes
preview/      standalone homepage prototype — open index.html
EXPLORER.html browse every file in the browser with design notes
CLAUDE.md     standing context, read automatically by Claude Code
deploy.sh     sync assets to the served web root
```

---

## Starting fresh from this repo

```bash
composer create-project laravel/laravel stellartech
cd stellartech
git clone <this-repo> _repo
cp -r _repo/app _repo/database _repo/resources _repo/routes _repo/public .
cp _repo/.env.example _repo/CLAUDE.md _repo/deploy.sh .
rm -rf _repo

rm database/migrations/0001_01_01_000000_create_users_table.php

cp .env.example .env
php artisan key:generate
# fill DB_* and MAIL_* in .env, then:
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=BadgeSeeder
php artisan db:seed --class=FounderSeeder   # then delete FOUNDER_PASSWORD from .env
```

Six config edits are still required — see `docs/01-database-setup.md` and
`docs/02-app-deploy.md`.

---

## Working on it with Claude Code

`CLAUDE.md` is read automatically, so context carries across sessions.

```bash
npm install -g @anthropic-ai/claude-code
cd stellartech
claude
```

For a first session, paste the brief in `docs/05-claude-code-prompt.md`.

---

## Deployment quirk

Hostinger shared hosting has no document-root setting, so the site uses the
split-public method:

```
~/domains/stellartechexplorers.com/app/          ← this project
~/domains/stellartechexplorers.com/public_html/  ← the served web root
```

**Editing `public/` changes nothing live until it is copied across.** Run `./deploy.sh`
after any CSS, JS or image change. If a change appears to do nothing, this is almost
always why.

---

## Security

- `.env` is gitignored. **Never commit secrets.** If one is committed, rotate it.
- Application code lives above the web root; only `public/` is served.
- Private media and all Rakhandar files live in `storage/app/private/`, streamed
  through an authenticated controller — never a direct URL.
- `APP_DEBUG=false` in production.
- Take a backup before every deploy: hPanel → Files → Backups, plus a phpMyAdmin export.

---

## Status

**Working:** 10 public pages · application → founder approval → account creation ·
login with lockout · member dashboard · 4 transactional emails · server-side cached
space news feed · 24-table schema · role middleware · Rakhandar four-gate access control

**Not built:** media uploads · task board · streak logging · reflections ·
credits ledger UI · Rakhandar Command Centre screens · password reset · events

Routes for the unbuilt parts sit commented out at the bottom of `routes/web.php`.

**Still open:** verify `ads.txt` at the web root · Privacy Policy and Terms need adult
review · Rakhandar hardware list should stay private (recommendation)
