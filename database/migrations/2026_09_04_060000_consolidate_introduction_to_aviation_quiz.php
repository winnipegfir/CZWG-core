<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $courseId = DB::table('academy_courses')->where('slug', 'introduction-to-aviation')->value('id');
        if (! $courseId) return;

        DB::table('academy_modules')->updateOrInsert(
            ['course_id' => $courseId, 'slug' => 'final-knowledge-check'],
            [
                'title' => 'Final Knowledge Check',
                'description' => 'Complete this cumulative assessment after reviewing all five Introduction to Aviation modules.',
                'google_slides_url' => null,
                'sort_order' => 6,
                'published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $finalModuleId = DB::table('academy_modules')->where('course_id', $courseId)->where('slug', 'final-knowledge-check')->value('id');
        DB::table('academy_quizzes')->updateOrInsert(
            ['module_id' => $finalModuleId],
            ['title' => 'Introduction to Aviation Knowledge Check', 'passing_score' => 80, 'published' => true, 'created_at' => now(), 'updated_at' => now()]
        );
        $finalQuizId = DB::table('academy_quizzes')->where('module_id', $finalModuleId)->value('id');

        $sourceModules = DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->where('id', '!=', $finalModuleId)
            ->orderBy('sort_order')
            ->pluck('id');
        $sourceQuizIds = DB::table('academy_quizzes')->whereIn('module_id', $sourceModules)->pluck('id');
        $questionIds = collect();
        foreach ($sourceModules as $sourceModuleId) {
            $sourceQuizId = DB::table('academy_quizzes')->where('module_id', $sourceModuleId)->value('id');
            if ($sourceQuizId) {
                $questionIds = $questionIds->concat(DB::table('academy_questions')->where('quiz_id', $sourceQuizId)->orderBy('sort_order')->orderBy('id')->pluck('id'));
            }
        }

        foreach ($questionIds as $order => $questionId) {
            DB::table('academy_questions')->where('id', $questionId)->update(['quiz_id' => $finalQuizId, 'sort_order' => $order, 'updated_at' => now()]);
        }
        DB::table('academy_quizzes')->whereIn('id', $sourceQuizIds)->delete();
    }

    public function down(): void
    {
        $courseId = DB::table('academy_courses')->where('slug', 'introduction-to-aviation')->value('id');
        if ($courseId) DB::table('academy_modules')->where('course_id', $courseId)->where('slug', 'final-knowledge-check')->delete();
    }
};
