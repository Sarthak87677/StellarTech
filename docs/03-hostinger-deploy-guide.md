# Getting the site live on Hostinger — step by step

Written assuming you have never deployed a website before. Follow it in order.
Nothing here is dangerous, and nothing here asks you to send anyone a password.

**About the VPN:** if your Hostinger plan includes a VPN, ignore it. It has nothing
to do with your website and you do not need it for any step below.

**Time needed:** about 90 minutes the first time, most of it waiting for DNS.

---

## Before you start — the one thing that must be fixed first

Your hPanel dashboard currently says *"Domain isn't connected to your website."*
Until that is fixed, nothing you upload will be reachable.

**Fix it:**

1. hPanel → **Domains** → click `stellartechexplorers.com`
2. Look for **Nameservers** (sometimes under "DNS / Nameservers")
3. They must be exactly:
   ```
   ns1.dns-parking.com
   ns2.dns-parking.com
   ```
4. If they are different, change them and save
5. Go back to **Websites → Manage → Dashboard** and click **Connect domain** on the red banner

Then wait. DNS changes take anywhere from 30 minutes to 24 hours. Do the next steps
while you wait — they don't depend on it.

---

## Step 1 — Create the database

hPanel → **Databases → Management**

1. **MySQL database name:** type `stellartech` (it becomes `u390930225_stellartech`)
2. **MySQL username:** type `stclub` (it becomes `u390930225_stclub`)
3. **Password:** click the dice/generate icon for a strong one
4. **Write all three down somewhere safe and private** — a password manager, or a
   note only you can see. You will need them once, in Step 4.
5. Click **Create**

> **Never type these into a chat, an email, a screenshot, or a file you commit to git.**
> Nobody helping you with this project ever needs them — including me.

Leave **Databases → Remote MySQL** completely empty. Your site connects locally.

---

## Step 2 — Get the code onto the server

You need Composer to install Laravel's dependencies, and that means SSH.

### 2a. Connect by SSH

hPanel → **Advanced → SSH Access**. It's already active. Copy the command shown there
and paste it into Terminal (Mac/Linux) or PowerShell (Windows 10+). It will ask for
your SSH password — the one you set in hPanel, not your Hostinger login.

> If you haven't changed your SSH password since sharing that screenshot earlier,
> do it now: **Advanced → SSH Access → Password → Change**.

### 2b. Create the Laravel project

Once connected, you'll see a prompt. Type these one at a time:

```bash
cd ~/domains/stellartechexplorers.com
composer create-project laravel/laravel app
```

That downloads Laravel. It takes a few minutes and prints a lot of text — that's normal.

If `composer` isn't found, try `php composer.phar` or `~/composer.phar` instead.

### 2c. Upload the project files

Easiest way: hPanel → **Files → File manager**, navigate to
`domains/stellartechexplorers.com/app/`, and drag the folders from the `laravel/`
folder in this package into the matching places:

| From this package | Goes into |
|---|---|
| `laravel/app/*` | `app/` |
| `laravel/database/migrations/*` | `database/migrations/` |
| `laravel/database/seeders/*` | `database/seeders/` |
| `laravel/resources/*` | `resources/` |
| `laravel/routes/web.php` | `routes/` (replace the existing one) |
| `laravel/public/images/*` | `public/images/` |
| `laravel/vite.config.js` | project root (replace) |
| `laravel/.env.example` | project root (replace) |

**Then delete this one file**, or migration will fail:
`database/migrations/0001_01_01_000000_create_users_table.php`

---

## Step 3 — Point the domain at the right folder

Laravel keeps its code private and serves only its `public/` folder. This is the
single most important security step: it's what stops anyone downloading your `.env`.

**Try this first** — hPanel, search for **"document root"**. If you find a setting you
can edit, change it to:

```
domains/stellartechexplorers.com/app/public
```

**If you can't change it**, do this instead in File Manager:

1. Copy everything inside `app/public/` into `public_html/`
2. Open `public_html/index.php` and find these two lines:
   ```php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
3. Change both `/../` to `/../app/`:
   ```php
   require __DIR__.'/../app/vendor/autoload.php';
   $app = require_once __DIR__.'/../app/bootstrap/app.php';
   ```

**Either way — check `ads.txt` is still in `public_html/` afterwards.** If it went
missing, get a fresh copy from your AdSense account and put it back.

---

## Step 4 — Settings and secrets

In SSH:

```bash
cd ~/domains/stellartechexplorers.com/app
cp .env.example .env
php artisan key:generate
```

Now edit `.env` — use File Manager's editor, it's easier than a terminal editor.
Fill in only these:

```
APP_URL=https://stellartechexplorers.com
APP_DEBUG=false

DB_DATABASE=u390930225_stellartech
DB_USERNAME=u390930225_stclub
DB_PASSWORD=          ← the password from Step 1

MAIL_PASSWORD=        ← your director@ mailbox password

FOUNDER_NAME=         ← your name
FOUNDER_EMAIL=director@stellartechexplorers.com
FOUNDER_PASSWORD=     ← invent one, at least 12 characters
```

`APP_DEBUG=false` matters. With it on, an error page shows visitors your database
details.

For mail settings, check hPanel → **Emails → Email Accounts → Connect devices**
for the exact SMTP host and port, and correct the `.env` if they differ.

---

## Step 5 — Build the database

```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=BadgeSeeder
php artisan db:seed --class=FounderSeeder
```

You should see a list of table names with "DONE" next to each.

**Now go back into `.env` and delete the `FOUNDER_PASSWORD` line.** Your account
exists; the password is stored as a hash and the plain text is no longer needed.

---

## Step 6 — Front-end assets

**No build step.** Hostinger shared hosting has no Node, and this project doesn't need
it. CSS and JS are plain files served from `public/assets/`, and Three.js loads from a
CDN. You already copied them in Step 2.

Trade-off worth knowing: assets aren't minified, and Three.js comes from cdnjs rather
than your own server. In exchange, you edit a file and refresh — no rebuild, no upload
dance. For a site this size the performance difference is negligible.

After editing any CSS or JS, bump `ASSET_VERSION` in `.env` so browsers pick up the
change instead of serving a cached copy.

---

## Step 7 — Wire up the last few settings

Three small file edits. All in File Manager.

**`bootstrap/app.php`** — find `->withMiddleware(function (Middleware $middleware) {`
and add inside it:

```php
$middleware->alias([
    'role' => \App\Http\Middleware\EnsureRole::class,
    'rakhandar.elevated' => \App\Http\Middleware\RakhandarElevated::class,
]);
```

**`composer.json`** — inside `"autoload"`, add:

```json
"files": ["app/helpers.php"]
```
then in SSH: `composer dump-autoload`

**`config/filesystems.php`** — inside `'disks' => [`, add:

```php
'public_media' => [
    'driver' => 'local',
    'root' => public_path('media'),
    'url' => env('APP_URL').'/media',
    'visibility' => 'public',
],
'private' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
    'visibility' => 'private',
],
```

**`config/mail.php`** — add near the bottom, inside the main array:
```php
'contact_to' => env('MAIL_CONTACT_TO', env('MAIL_FROM_ADDRESS')),
```

**`config/services.php`** — add inside the array:
```php
'news' => ['cache_minutes' => env('NEWS_CACHE_MINUTES', 15)],
```

Then:

```bash
php artisan optimize
chmod -R 775 storage bootstrap/cache
```

---

## Step 8 — Turn on HTTPS

hPanel → search **"Force HTTPS"** → turn it on. Your SSL certificate is already
active and never expires.

---

## Step 9 — Test it, in this order

Visit `https://stellartechexplorers.com` and work down the list:

1. ✅ Homepage loads, solar system animates, headline words fly in
2. ✅ `https://stellartechexplorers.com/ads.txt` still shows your AdSense line
3. ✅ About, Projects, Rakhandar, Credits, Privacy, Terms all load
4. ✅ Contact form sends — check your `director@` inbox
5. ✅ Apply form submits, and you get **two** emails (applicant + founder copy)
6. ✅ `/login` — sign in with your founder email and password
7. ✅ Dashboard loads and shows your role as "Founder"
8. ✅ `/manage/applications` shows the test application you just submitted
9. ✅ Approve it with role "Member" — the applicant gets an email with a password
10. ✅ Sign out, sign in as that new member, confirm you **cannot** reach
    `/manage/applications` (should be a 403)

**Step 10 is the important one.** If a member can reach a founder page, stop and
tell me before going further.

---

## If something breaks

**White page or "500 error":** the log is at `storage/logs/laravel.log`. Open it in
File Manager and read the last few lines. Don't turn `APP_DEBUG` on to investigate —
read the log instead.

**"Class not found":** run `composer dump-autoload` then `php artisan optimize:clear`.

**Changes not appearing:** `php artisan optimize:clear`, and clear your browser cache.

**"Permission denied" on uploads:** `chmod -R 775 storage`

**Emails not arriving:** check the SMTP port matches hPanel exactly. Port 465 needs
`MAIL_ENCRYPTION=ssl`; port 587 needs `tls`. Check spam.

---

## Backups — do this before every future change

hPanel → **Files → Backups**. Yours run weekly, which is not often enough while
you're actively building. Before any change:

1. **Files → Backups → Generate new backup**
2. **Databases → phpMyAdmin → Export → Go** — saves a `.sql` file to your computer

To roll back: restore the file backup in hPanel, then re-import that `.sql` through
phpMyAdmin's Import tab.

---

## What is live after this

**Working:** homepage with live space news, About, Projects, Rakhandar overview,
Credits, Privacy, Terms, Contact form, application form, founder approval flow that
creates real member accounts, login, and the member dashboard.

**Not yet built:** media uploads, task board, streak logging, reflections, credits
ledger, and the Rakhandar Command Centre. Their routes are in `routes/web.php`,
commented out, so the app boots cleanly without them.

---

## Before applying to AdSense

- All public pages have real content — done
- Privacy policy and Terms exist — done, **but have an adult review them first**
- Contact page works — done
- `ads.txt` at the domain root — verify after deploying
- No ads on `/login` or anything under `/dashboard` — the layout already handles this
