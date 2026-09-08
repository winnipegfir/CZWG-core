<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('fa-graduation-cap');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('published')->default(false);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('academy_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('academy_courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('google_slides_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->unique(['course_id', 'slug']);
        });

        Schema::create('academy_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->unique()->constrained('academy_modules')->cascadeOnDelete();
            $table->string('title')->default('Knowledge Check');
            $table->unsignedTinyInteger('passing_score')->default(80);
            $table->boolean('published')->default(false);
            $table->timestamps();
        });

        Schema::create('academy_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('academy_quizzes')->cascadeOnDelete();
            $table->text('question');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('academy_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('academy_questions')->cascadeOnDelete();
            $table->text('answer');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_answers');
        Schema::dropIfExists('academy_questions');
        Schema::dropIfExists('academy_quizzes');
        Schema::dropIfExists('academy_modules');
        Schema::dropIfExists('academy_courses');
    }
};
