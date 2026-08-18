# Milestone 2B — Laravel application

The homepage is now a real Laravel app: Blade views, server-rendered news, DB-driven
media and leadership, and the full route map with middleware stacks applied.

## Install over the 2A scaffold

```bash
cp -r site/resources/*  resources/
cp -r site/app/*        app/
cp    site/routes/web.php routes/
cp    site/vite.config.js .
cp -r site/public/images  public/

composer require laravel/framework
npm install
npm install three
```

Register the settings helper in `composer.json`:

```json
"autoload": {
    "files": ["app/helpers.php"]
}
```
then `composer dump-autoload`.

Add the public media disk to `config/filesystems.php`:

```php
'public_media' => [
    'driver' => 'local',
    'root'   => public_path('media'),
    'url'    => env('APP_URL').'/media',
    'visibility' => 'public',
],
'private' => [
    'driver' => 'local',
    'root'   => storage_path('app/private'),   // OUTSIDE public_html
    'visibility' => 'private',
],
```

Add news cache config to `config/services.php`:

```php
'news' => ['cache_minutes' => env('NEWS_CACHE_MINUTES', 15)],
```

## Build and run

```bash
npm run build        # production assets into public/build
php artisan optimize
php artisan serve    # local check
```

## Still to write (controllers referenced by routes/web.php)

The route map is complete and deliberately declares controllers before they exist,
so the security shape is fixed first and features fill in behind it:

`ApplicationController`, `ContactController`, `AuthController`, `DashboardController`,
`TaskController`, `StreakController`, `ReflectionController`, `CreditController`,
`MemberMediaController`, `BoardController`, `AnnouncementController`,
`MediaReviewController`, `ApplicationReviewController`, `MemberController`,
`SettingController`, `AccessLogController`, `RakhandarAccessController`,
`RakhandarController`, `RakhandarFileController`.

Comment out the route groups you have not built yet, or `php artisan make:controller`
each one as you go. Build order: Auth → Application → Dashboard → Media → Rakhandar.

## Deployment order (do NOT skip)

1. Deploy to `test.stellartechexplorers.com` first, with `noindex`
2. Manual backup (files + SQL dump) before every production push
3. Verify `https://stellartechexplorers.com/ads.txt` still returns after deploy
4. `APP_DEBUG=false`, Force HTTPS on
5. Test in this order: page load → contact form → apply form → approval email →
   login → OTP delivery → upload → **private file access as the WRONG role (must fail)**
