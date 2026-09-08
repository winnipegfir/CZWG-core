<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $decks = [
            'introduction-to-radar' => [
                'title' => 'Introduction to Radar',
                'url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQtRBnMgP5GHpJvSUxbyg0BqDdu9zchuepRHLtXuLvp_WFJI2HKb1j5ypMjoEcPOWXEDeddlC05GLor/pubembed?start=false&loop=false&delayms=3000',
            ],
            'departure' => [
                'title' => 'Departure',
                'url' => 'https://docs.google.com/presentation/d/e/2PACX-1vSmS2eaNqpmtI9FmhugMUHlxPiy-HBwD5eJV6rqlNmmcq0pa6-8bJtHmK7-_Z-Fo9scAqd760hwE8pW/pubembed?start=false&loop=false&delayms=3000',
            ],
            'arrival' => [
                'title' => 'Arrival',
                'url' => 'https://docs.google.com/presentation/d/e/2PACX-1vR8xi7K2EM04QYXPCw2q2ZeZVQ631Fih6mXDxpdOts0fYsMFIM84Kv5hveDJZwh7dqsF3h4kNaRul5V/pubembed?start=false&loop=false&delayms=3000',
            ],
            'terminal' => [
                'title' => 'Terminal',
                'url' => 'https://docs.google.com/presentation/d/e/2PACX-1vQJHcZYN98GpeaO3h4VLSYQeqGzsKlAsc5It0dHd5cuMVmqpvylvhWRNN6FkPnF0mXBs_633uDEPA1b/pubembed?start=false&loop=false&delayms=3000',
            ],
            'center' => [
                'title' => 'Center',
                'url' => 'https://docs.google.com/presentation/d/e/2PACX-1vTNG93eDQ9vfaMkUWhxuOiBPQMqXQOpsvNauoBVWfmXRwl29eXE0P9K8O08wPvVpe0L_kEN6xK_lJiM/pubembed?start=false&loop=false&delayms=3000',
            ],
        ];

        foreach ($decks as $courseSlug => $deck) {
            $courseId = DB::table('academy_courses')->where('slug', $courseSlug)->value('id');
            if (! $courseId) {
                continue;
            }

            $moduleSlug = Str::slug($deck['title']);
            DB::table('academy_modules')->updateOrInsert(
                ['course_id' => $courseId, 'slug' => $moduleSlug],
                [
                    'title' => $deck['title'],
                    'description' => 'Course presentation.',
                    'google_slides_url' => $deck['url'],
                    'sort_order' => 1,
                    'published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Keep modules/URLs if rolled back; administrators may have edited them after deployment.
    }
};
