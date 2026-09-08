<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modules = DB::table('academy_modules')
            ->where(function ($q) {
                $q->whereIn('slug', ['final-knowledge-check', 'final-self-assessment'])
                  ->orWhere('title', 'like', '%Final Knowledge Check%')
                  ->orWhere('title', 'like', '%Final Self Assessment%');
            })
            ->get();

        foreach ($modules as $module) {
            DB::table('academy_modules')->where('id', $module->id)->update([
                'title' => 'Self Assessment',
                'slug' => 'final-self-assessment',
                'description' => 'Complete this cumulative self assessment after reviewing every module in the course.',
                'updated_at' => now(),
            ]);

            DB::table('academy_quizzes')->where('module_id', $module->id)->update([
                'title' => 'Self Assessment',
                'updated_at' => now(),
            ]);
        }

        DB::table('academy_quizzes')
            ->where(function ($q) {
                $q->where('title', 'like', '%Final Knowledge Check%')
                  ->orWhere('title', 'like', '%Knowledge Check%')
                  ->orWhere('title', 'like', '%Final Self Assessment%');
            })
            ->update([
                'title' => 'Self Assessment',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Terminology-only migration; intentionally not reverted.
    }
};
