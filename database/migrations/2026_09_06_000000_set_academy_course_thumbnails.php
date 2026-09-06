<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const THUMBNAILS = [
        'introduction-to-air-traffic-services' => '/academy-assets/course-thumbnails/introduction-to-air-traffic-services.png',
        'introduction-to-aviation' => '/academy-assets/course-thumbnails/introduction-to-aviation.png',
        'software-setup' => '/academy-assets/course-thumbnails/software-setup.png',
        'clearance-delivery' => '/academy-assets/course-thumbnails/clearance-delivery.png',
        'ground' => '/academy-assets/course-thumbnails/ground.png',
        'tower' => '/academy-assets/course-thumbnails/tower.png',
        'advanced-tower' => '/academy-assets/course-thumbnails/advanced-tower.png',
        'introduction-to-radar' => '/academy-assets/course-thumbnails/introduction-to-radar.png',
        'departure' => '/academy-assets/course-thumbnails/departure.png',
        'arrival' => '/academy-assets/course-thumbnails/arrival.png',
        'terminal' => '/academy-assets/course-thumbnails/terminal.png',
        'center' => '/academy-assets/course-thumbnails/center.png',
    ];

    public function up(): void
    {
        foreach (self::THUMBNAILS as $slug => $thumbnail) {
            DB::table('academy_courses')
                ->where('slug', $slug)
                ->update(['thumbnail' => $thumbnail]);
        }
    }

    public function down(): void
    {
        foreach (self::THUMBNAILS as $slug => $thumbnail) {
            DB::table('academy_courses')
                ->where('slug', $slug)
                ->where('thumbnail', $thumbnail)
                ->update(['thumbnail' => null]);
        }
    }
};
