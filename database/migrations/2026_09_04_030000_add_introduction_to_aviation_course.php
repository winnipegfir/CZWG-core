<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('academy_courses')->updateOrInsert(
            ['slug' => 'introduction-to-aviation'],
            [
                'title' => 'Introduction to Aviation',
                'description' => 'An introduction to aviation fundamentals for new Winnipeg FIR students.',
                'icon' => 'fa-plane',
                'sort_order' => 2,
                'published' => true,
                'default_enrollment' => true,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $courseId = DB::table('academy_courses')->where('slug', 'introduction-to-aviation')->value('id');

        $modules = [
            ['title' => 'The Basics', 'slug' => 'the-basics', 'id' => '1YuNEyh0vt5g2qxAAcSNli5YV1tMK1RnoRYCGm7XuoJU'],
            ['title' => 'Radio Telecommunication', 'slug' => 'radio-telecommunication', 'id' => '1QVhgmSr_FUHglmFu7AxE_h2PdH5ydTPz-4hoXGjGQ1I'],
            ['title' => 'Flight Planning', 'slug' => 'flight-planning', 'id' => '1P23zkP-MaBB2QLop0ozJoyGJOFriVawHjOcq9riMqhI'],
            ['title' => 'Airspace', 'slug' => 'airspace', 'id' => '1K1KGgugiIKlD9v4NmIqTraC3Gt4lWKJYgWU1bJlScWo'],
            ['title' => 'Aviation Weather', 'slug' => 'aviation-weather', 'id' => '1uvUC6emfzdbstMxgcjcHtOcqoO4pTpVPD82CrFdRUUk'],
        ];

        foreach ($modules as $index => $module) {
            DB::table('academy_modules')->updateOrInsert(
                ['course_id' => $courseId, 'slug' => $module['slug']],
                [
                    'title' => $module['title'],
                    'description' => 'Module '.($index + 1).' of Introduction to Aviation.',
                    'google_slides_url' => 'https://docs.google.com/presentation/d/'.$module['id'].'/edit',
                    'sort_order' => $index + 1,
                    'published' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('academy_courses')->where('slug', 'introduction-to-aviation')->delete();
    }
};
