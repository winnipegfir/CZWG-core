<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_questions', function (Blueprint $table) {
            $table->string('type', 20)->default('multiple_choice')->after('question');
            $table->unsignedTinyInteger('points')->default(1)->after('type');
            $table->text('rubric')->nullable()->after('explanation');
        });

        Schema::create('academy_quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('academy_quizzes')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->string('status', 20)->default('pending_review');
            $table->unsignedSmallInteger('automatic_score')->default(0);
            $table->unsignedSmallInteger('manual_score')->default(0);
            $table->unsignedSmallInteger('maximum_score')->default(0);
            $table->unsignedInteger('graded_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            $table->index(['quiz_id', 'user_id']);
        });

        Schema::create('academy_quiz_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('academy_quiz_submissions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('academy_questions')->cascadeOnDelete();
            $table->foreignId('answer_id')->nullable()->constrained('academy_answers')->nullOnDelete();
            $table->text('written_response')->nullable();
            $table->unsignedTinyInteger('awarded_points')->nullable();
            $table->text('grader_feedback')->nullable();
            $table->timestamps();
            $table->unique(['submission_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_quiz_responses');
        Schema::dropIfExists('academy_quiz_submissions');
        Schema::table('academy_questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'points', 'rubric']);
        });
    }
};
