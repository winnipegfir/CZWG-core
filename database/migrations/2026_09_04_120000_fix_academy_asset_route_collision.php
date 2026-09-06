<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academy_modules') || ! Schema::hasColumn('academy_modules', 'content')) {
            return;
        }

        DB::table('academy_modules')
            ->where('content', 'like', '%/academy/media/%')
            ->orderBy('id')
            ->chunkById(100, function ($modules) {
                foreach ($modules as $module) {
                    DB::table('academy_modules')
                        ->where('id', $module->id)
                        ->update([
                            'content' => str_replace('/academy/media/', '/academy-assets/media/', $module->content),
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('academy_modules') || ! Schema::hasColumn('academy_modules', 'content')) {
            return;
        }

        DB::table('academy_modules')
            ->where('content', 'like', '%/academy-assets/media/%')
            ->orderBy('id')
            ->chunkById(100, function ($modules) {
                foreach ($modules as $module) {
                    DB::table('academy_modules')
                        ->where('id', $module->id)
                        ->update([
                            'content' => str_replace('/academy-assets/media/', '/academy/media/', $module->content),
                        ]);
                }
            });
    }
};
