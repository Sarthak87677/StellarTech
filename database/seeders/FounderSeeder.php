<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Creates the first founder account from environment variables.
 *
 * SECURITY: no password is ever written into this file or into version control.
 * Set FOUNDER_EMAIL and FOUNDER_PASSWORD in .env, run the seeder once,
 * then REMOVE FOUNDER_PASSWORD from .env and change the password in the app.
 */
class FounderSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('FOUNDER_EMAIL');
        $password = env('FOUNDER_PASSWORD');
        $name     = env('FOUNDER_NAME', 'Founder');

        if (! $email || ! $password) {
            $this->command->warn('FounderSeeder skipped: set FOUNDER_EMAIL and FOUNDER_PASSWORD in .env');
            return;
        }

        if (strlen($password) < 12) {
            $this->command->error('FounderSeeder aborted: password must be at least 12 characters.');
            return;
        }

        $roleId = DB::table('roles')->where('slug', 'founder')->value('id');

        if (! $roleId) {
            $this->command->error('FounderSeeder aborted: run RoleSeeder first.');
            return;
        }

        $userId = DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role_id' => $roleId,
                'status' => 'active',
                'joined_at' => now(),
                'email_verified_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $id = DB::table('users')->where('email', $email)->value('id');
        DB::table('credits')->updateOrInsert(['user_id' => $id], ['updated_at' => now(), 'created_at' => now()]);
        DB::table('user_streaks')->updateOrInsert(['user_id' => $id], ['updated_at' => now(), 'created_at' => now()]);

        $this->command->info('Founder account ready. Now remove FOUNDER_PASSWORD from .env.');
    }
}
