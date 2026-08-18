<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'slug' => 'member', 'name' => 'Member', 'level' => 10,
                'permissions' => ['dashboard.view', 'task.own', 'streak.submit',
                                  'reflection.submit', 'media.upload', 'credit.view.own'],
            ],
            [
                'slug' => 'oc_member', 'name' => 'OC Member', 'level' => 20,
                'permissions' => ['dashboard.view', 'task.own', 'task.board', 'task.assign',
                                  'streak.submit', 'streak.prereview', 'reflection.submit',
                                  'media.upload', 'media.prereview', 'announcement.create',
                                  'credit.view.own'],
            ],
            [
                'slug' => 'selected_oc', 'name' => 'Selected OC Member', 'level' => 30,
                'permissions' => ['dashboard.view', 'task.own', 'task.board', 'task.assign',
                                  'streak.submit', 'streak.review', 'reflection.review',
                                  'media.upload', 'media.approve', 'announcement.create',
                                  'application.view', 'credit.view.own',
                                  'rakhandar.access', 'rakhandar.drone.authorize',
                                  'log.view.scoped'],
            ],
            [
                'slug' => 'founder', 'name' => 'Founder', 'level' => 40,
                'permissions' => ['*'],
            ],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $r['slug']],
                [
                    'name' => $r['name'],
                    'level' => $r['level'],
                    'permissions' => json_encode($r['permissions']),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
