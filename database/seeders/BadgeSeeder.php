<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['consistency',   'Steady Hand',      'Logged approved work on seven consecutive active days.'],
            ['teamwork',      'Second Pair',      'Contributed to another member\'s task and had it acknowledged.'],
            ['research',      'Groundwork',       'Logged twenty approved research hours on a single project.'],
            ['engineering',   'It Actually Works','Built and demonstrated a working subsystem.'],
            ['documentation', 'Written Down',     'Produced documentation another member used without asking questions.'],
            ['presentation',  'Clear Signal',     'Presented project work to the club or an external audience.'],
            ['innovation',    'New Route',        'Proposed an approach the team adopted.'],
            ['leadership',    'Held the Line',    'Led a project team through a full build cycle.'],
        ];

        foreach ($badges as [$category, $name, $description]) {
            DB::table('badges')->updateOrInsert(
                ['slug' => $category . '-' . str($name)->slug()],
                [
                    'name' => $name,
                    'description' => $description,
                    'category' => $category,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
