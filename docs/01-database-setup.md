# Milestone 2A — Database Foundation

7 migrations (24 tables), 5 seeders, 2 middleware, 1 audit service, 1 env template.

## Install

Run these in your project folder. **Never paste passwords into a chat.**

```bash
# 1. Create the Laravel project (skip if you already have one)
composer create-project laravel/laravel stellartech
cd stellartech

# 2. Copy this scaffold in
cp -r /path/to/scaffold/database/migrations/*  database/migrations/
cp -r /path/to/scaffold/database/seeders/*     database/seeders/
cp -r /path/to/scaffold/app/Http/Middleware/*  app/Http/Middleware/
cp -r /path/to/scaffold/app/Services/*         app/Services/
cp    /path/to/scaffold/.env.example           .env.example

# 3. IMPORTANT — remove Laravel's default users migration, ours replaces it
rm database/migrations/0001_01_01_000000_create_users_table.php
rm database/migrations/0001_01_01_000001_create_cache_table.php  # optional, keep if you want cache table
```

## Register the middleware

`bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureRole::class,
        'rakhandar.elevated' => \App\Http\Middleware\RakhandarElevated::class,
    ]);
})
```

## Configure and migrate

```bash
cp .env.example .env
php artisan key:generate
# now edit .env and fill DB_DATABASE, DB_USERNAME, DB_PASSWORD

php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=BadgeSeeder

# Set FOUNDER_EMAIL and FOUNDER_PASSWORD in .env, then:
php artisan db:seed --class=FounderSeeder
# then DELETE FOUNDER_PASSWORD from .env
```

Verify: `php artisan migrate:status` should show 7 green, and `roles` should hold 4 rows.

---

## What the schema encodes

**Role hierarchy** is numeric (`level` 10/20/30/40) so comparisons are cheap, but
authorization checks the **slug**, never the number — a level comparison would silently
grant founder powers to anyone whose level got bumped.

**Rakhandar tables have no public code path.** The public Rakhandar page reads curated
static content. It never queries `rakhandar_*`. A query bug therefore cannot leak scan
data, because there is no query to go wrong.

**Monument coordinates live in `monuments`,** flagged PRIVATE, so comparison scans key
off a real record instead of a free-text name — and location never travels with a scan
row that might be rendered somewhere careless.

**`media_items` has both `path` and `public_path`.** Uploads land on the private disk.
`public_path` is written *only* on public approval. If it is null, the file has never
been published, regardless of what any status column says.

**`credit_transactions` stores `balance_after`.** Balances are reconstructable from the
ledger, so a bad write is detectable instead of invisible. Every row requires a `reason`.

**`otp_codes` stores `code_hash`,** never the code. Same for `users.rakhandar_pass_hash`.
Neither is ever emailed or logged.

**`access_logs` records denials as well as grants.** A denial pattern is the signal you
actually want; successes alone tell you nothing about attacks.

---

## Deliberately deferred to Phase 2

Events/workshops, monthly PDF reports, digital-twin viewer, automated comparison
generation, telemetry ingest, Razorpay. Feature flags for all of them are seeded **off**.

---

## Next milestone (2B)

Eloquent models with relationships and casts, `routes/web.php` skeleton with the
middleware stacks applied, and the homepage ported from `index.html` into Blade
components. Deploy that to `test.stellartechexplorers.com` before touching the root domain.
