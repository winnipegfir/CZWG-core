<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_courses', function (Blueprint $table) {
            $table->boolean('default_enrollment')->default(false)->after('published');
        });

        Schema::create('academy_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreignId('course_id')->constrained('academy_courses')->cascadeOnDelete();
            $table->unsignedInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('assigned_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_enrollments');
        Schema::table('academy_courses', function (Blueprint $table) {
            $table->dropColumn('default_enrollment');
        });
    }
};
