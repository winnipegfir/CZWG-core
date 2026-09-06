<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('academy_quiz_submissions', 'final_score')) {
            Schema::table('academy_quiz_submissions', function (Blueprint $table) {
                $table->unsignedInteger('final_score')->nullable()->after('manual_score');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('academy_quiz_submissions', 'final_score')) {
            Schema::table('academy_quiz_submissions', function (Blueprint $table) {
                $table->dropColumn('final_score');
            });
        }
    }
};
