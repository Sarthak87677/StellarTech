<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Feature flags — all risky features OFF until explicitly enabled
            ['key' => 'payments_enabled',      'value' => '0', 'group' => 'features'],
            ['key' => 'razorpay_enabled',      'value' => '0', 'group' => 'features'],
            ['key' => 'drone_module',          'value' => '0', 'group' => 'features'],
            ['key' => 'leaderboard_visible',   'value' => '0', 'group' => 'features'],
            ['key' => 'applications_open',     'value' => '1', 'group' => 'features'],
            ['key' => 'news_feed_enabled',     'value' => '1', 'group' => 'features'],

            // Security policy
            ['key' => 'otp_ttl_seconds',       'value' => '300',  'group' => 'security'],
            ['key' => 'otp_max_attempts',      'value' => '5',    'group' => 'security'],
            ['key' => 'elevated_ttl_seconds',  'value' => '900',  'group' => 'security'],
            ['key' => 'session_idle_seconds',  'value' => '1800', 'group' => 'security'],
            ['key' => 'login_max_attempts',    'value' => '5',    'group' => 'security'],
            ['key' => 'lockout_seconds',       'value' => '900',  'group' => 'security'],

            // Uploads
            ['key' => 'upload_max_image_mb',   'value' => '8',  'group' => 'uploads'],
            ['key' => 'upload_max_video_mb',   'value' => '64', 'group' => 'uploads'],
            ['key' => 'prefer_video_embed',    'value' => '1',  'group' => 'uploads'],

            // Public site content (safe to expose)
            ['key' => 'contact_email', 'value' => 'director@stellartechexplorers.com',
             'group' => 'site', 'is_public' => 1],
            ['key' => 'founder_bio',   'value' => '', 'group' => 'site', 'is_public' => 1],
            ['key' => 'founder_name',  'value' => '', 'group' => 'site', 'is_public' => 1],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                [
                    'value' => $s['value'],
                    'group' => $s['group'],
                    'is_public' => $s['is_public'] ?? 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
